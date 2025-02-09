<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Training;
use Illuminate\Support\Facades\Auth;
use App\Models\TrainingStatus;
use Carbon\Carbon;
use App\Models\TrainingSchedule;
use App\Models\TrainingReservation;
use Illuminate\Support\Facades\Log;

class ReservationController extends Controller
{
    public function storeReservation(Request $request, $id) {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required',
        ]);
    
        $user = Auth::user();
        $training = Training::findOrFail($id);
    
    
        // Obtener el pago del usuario para este entrenamiento
        $payment = Payment::where('user_id', $user->id)->where('training_id', $id)->first();
    
        if (!$payment) {
            return back()->with('error', 'No se encontró el pago para este entrenamiento.');
        }
    
        $weeklySessions = $payment->weekly_sessions;
    
        // 📌 **Regla 1: Solo permitir reservas dentro de los próximos 4 días**
        $today = Carbon::today();
        $maxReservationDate = $today->copy()->addDays(4);
    
        if (Carbon::parse($request->date)->greaterThan($maxReservationDate)) {
            return back()->with('error', 'Solo puedes reservar clases dentro de los próximos 4 días.');
        }
    
        // 📌 **Regla 2: Validar que la fecha seleccionada sea en un día disponible**
        $requestedDay = Carbon::parse($request->date)->format('l'); // Obtiene el día de la semana en inglés
    
        // Convertir los nombres de días a español
        $daysMap = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo'
        ];
        $requestedDaySpanish = $daysMap[$requestedDay];
    
        // Obtener los días en los que este entrenamiento está disponible
        $availableDays = TrainingSchedule::where('training_id', $id)->pluck('day')->toArray(); // Ejemplo: ['Martes', 'Jueves']
    
        if (!in_array($requestedDaySpanish, $availableDays)) {
            return back()->with('error', "Este entrenamiento solo está disponible los días: " . implode(', ', $availableDays) . ".");
        }
    
        // 📌 **Regla 3: Contar las reservas activas de la semana**
        $startOfWeek = Carbon::now()->startOfWeek(); // Lunes
        $endOfWeek = Carbon::now()->endOfWeek(); // Domingo
    
        $activeReservations = TrainingReservation::where('user_id', $user->id)
            ->where('training_id', $id)
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->where(function ($query) {
                $query->whereNull('canceled_at') // No canceladas
                      ->orWhere('canceled_at', '>=', Carbon::now()->subHours(12)); // Canceladas después de 12 horas
            })
            ->count();
    
        if ($activeReservations >= $weeklySessions) {
            return back()->with('error', 'Ya has reservado todas tus clases de esta semana.');
        }
        // 📌 **Verificar si el usuario ya tiene una reserva en la misma fecha y hora**
        $existingReservation = TrainingReservation::where('user_id', $user->id)
            ->where('training_id', $id)
            ->where('date', $request->date)
            ->where('time', $request->time)
            ->first();
    
        if ($existingReservation) {
            return back()->with('error', 'Ya tienes una reserva para este entrenamiento en la misma fecha y horario.');
        }
    
        // 📌 **Regla 4: Verificar si hay cupos disponibles**
        $currentReservations = TrainingReservation::where('training_id', $id)
            ->where('date', $request->date)
            ->where('time', $request->time)
            ->count();
    
        $availableSpots = $training->available_spots - $currentReservations;
        if ($availableSpots <= 0) {
        return back()->with('error', 'No hay cupos disponibles para este horario.');
        }
    
        // 📌 **Crear la reserva**
        TrainingReservation::create([
            'user_id' => $user->id,
            'training_id' => $id,
            'date' => $request->date,
            'time' => $request->time,
        ]);
    
        return redirect()->route('my.trainings')->with('success', 'Reserva realizada con éxito.');
    }
    public function cancelReservation($id) {
        $reservation = TrainingReservation::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
    
        $reservationTime = Carbon::parse($reservation->date . ' ' . $reservation->time);
        $now = Carbon::now();
    
        if ($now->diffInHours($reservationTime) >= 12) {
            // Cancelación con más de 12 horas → Se elimina la reserva
            $reservation->delete();
            return back()->with('success', 'Reserva cancelada sin penalización.');
        } else {
            // Cancelación con menos de 12 horas → Se marca como cancelada (pero cuenta como usada)
            $reservation->update(['canceled_at' => $now]);
            return back()->with('warning', 'Cancelaste tu reserva con menos de 12 horas de anticipación. Se te contará como una clase utilizada.');
        }
    }
    public function getAvailableTimes(Request $request, $id) {
        $date = Carbon::parse($request->date);
        $requestedDay = $date->format('l'); // Día de la semana en inglés
    
        // Mapeo de nombres de días en inglés a español
        $daysMap = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo'
        ];
        $requestedDaySpanish = $daysMap[$requestedDay];
    
        \Log::info("🔍 Verificando disponibilidad para {$requestedDaySpanish} ({$request->date})");
    
        // Obtener los horarios disponibles para ese día
        $availableTimes = TrainingSchedule::where('training_id', $id)
            ->where('day', $requestedDaySpanish)
            ->get(['id', 'start_time', 'end_time']);
    
        // 🚨 **Filtrar horarios suspendidos**
        foreach ($availableTimes as $time) {
            $isSuspended = TrainingStatus::where('training_schedule_id', $time->id)
                ->where('date', $request->date)
                ->where('status', 'suspended')
                ->exists();
    
            if ($isSuspended) {
                \Log::info("🚨 Clase suspendida detectada: No se mostrará en la selección ({$request->date})");
                continue; // No agregar horarios suspendidos
            }
    
            // Obtener cupos disponibles por horario
            $reservationsCount = TrainingReservation::where('training_id', $id)
                ->where('date', $request->date)
                ->where('time', $time->start_time)
                ->count();
    
            $availableSpots = Training::find($id)->available_spots - $reservationsCount;
            $time->available_spots = max($availableSpots, 0); // Evita valores negativos
        }
    
        // 🚨 **Filtrar horarios suspendidos del array antes de devolverlo**
        $availableTimes = $availableTimes->reject(function ($time) use ($request) {
            return TrainingStatus::where('training_schedule_id', $time->id)
                ->where('date', $request->date)
                ->where('status', 'suspended')
                ->exists();
        });
    
        return response()->json($availableTimes);
    }
    public function reserveTrainingView($id) {
        $training = Training::with('schedules')->findOrFail($id);
    
        // Obtener horarios disponibles
        $availableSchedules = TrainingSchedule::where('training_id', $id)->get();
    
        return view('student.training.reserve-training', compact('training', 'availableSchedules'));
    }

    // Me parece que no lo uso
    public function reservationDetail($id, $date)
    {
        $training = Training::with('park')->findOrFail($id);
        $reservations = TrainingReservation::where('training_id', $id)->where('date', $date)->get();

        return view('trainings.reservation-detail', compact('training', 'date', 'reservations'));
    }
}

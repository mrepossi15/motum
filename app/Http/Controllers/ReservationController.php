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
    
        // 📌 **Actualizar reservas pasadas a 'completed' si la clase ya comenzó**
        $now = Carbon::now();
        TrainingReservation::where('user_id', $user->id)
            ->whereNull('canceled_at')
            ->where('status', 'active')
            ->whereRaw("STR_TO_DATE(CONCAT(date, ' ', time), '%Y-%m-%d %H:%i:%s') < ?", [$now])
            ->update(['status' => 'completed']);
    
        // 📌 **Obtener el pago del usuario para este entrenamiento**
        $payment = Payment::where('user_id', $user->id)->where('training_id', $id)->first();
        if (!$payment) {
            return back()->with('error', 'No se encontró el pago para este entrenamiento.');
        }
        $weeklySessions = $payment->weekly_sessions;
    
        // 📌 **Contar las reservas completadas y no-show de la semana**
        $reservationsThisWeek = TrainingReservation::where('user_id', $user->id)
            ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->whereIn('status', ['completed', 'no-show']) // ✅ Solo contar reservas ya usadas
            ->count();
    
        if ($reservationsThisWeek >= $weeklySessions) {
            return back()->with('error', 'Ya has completado todas tus clases de esta semana.');
        }
    
        // 📌 **Verificar si el usuario tiene una reserva ACTIVA**
        $existingReservation = TrainingReservation::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();
    
        if ($existingReservation) {
            return back()->with('error', 'Solo puedes tener una reserva activa a la vez.');
        }
    
        // 📌 **Verificar si hay cupos disponibles**
        $currentReservations = TrainingReservation::where('training_id', $id)
            ->where('date', $request->date)
            ->where('time', $request->time)
            ->count();
    
        if ($currentReservations >= $training->available_spots) {
            return back()->with('error', 'No hay cupos disponibles para este horario.');
        }
    
        // 📌 **Crear la reserva**
        TrainingReservation::create([
            'user_id' => $user->id,
            'training_id' => $id,
            'date' => $request->date,
            'time' => $request->time,
            'status' => 'active', // ✅ Nueva reserva en estado activo
        ]);
    
        return redirect()->route('student.training.myTrainings')->with('success', 'Reserva realizada con éxito.');
    }
    public function cancelReservation($id) {
        $reservation = TrainingReservation::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
    
        $reservationTime = Carbon::parse($reservation->date . ' ' . $reservation->time);
        $now = Carbon::now();
    
        if ($now->diffInHours($reservationTime) < 6) {
            return back()->with('error', 'No puedes cancelar una reserva con menos de 6 horas de anticipación.');
        }
    
        $reservation->delete();
    
        return back()->with('success', 'Reserva cancelada correctamente.');
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
    
        \Log::info("🔍 Buscando horarios para {$requestedDaySpanish} ({$request->date}) en entrenamiento {$id}");
    
        // Obtener la hora actual si la fecha es hoy
        $currentTime = Carbon::now()->format('H:i:s');
    
        // Obtener los horarios disponibles para ese día
        $availableTimes = TrainingSchedule::where('training_id', $id)
            ->where('day', $requestedDaySpanish)
            ->when($date->isToday(), function ($query) use ($currentTime) {
                return $query->where('start_time', '>=', $currentTime);
            })
            ->get(['id', 'start_time', 'end_time']);
    
        // 🚨 **Filtrar horarios suspendidos**
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
    public function reservationDetail($id, $date, Request $request)
    {
        $selectedTime = $request->query('time'); // Obtener la hora seleccionada
    
        $training = Training::with('park')->findOrFail($id);
    
        // Filtrar las reservas por fecha y hora específica
        $reservations = TrainingReservation::where('training_id', $id)
            ->where('date', $date)
            ->when($selectedTime, function ($query) use ($selectedTime) {
                return $query->where('time', $selectedTime);
            })
            ->get();
    
        return view('trainings.reservation-detail', compact('training', 'date', 'reservations', 'selectedTime'));
    }

    public function updateReservationStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:completed,no-show',
    ]);

    $reservation = TrainingReservation::findOrFail($id);

    // Verificar que el usuario que intenta cambiar el estado sea el entrenador de la clase
    if (Auth::user()->id !== $reservation->training->trainer_id) {
        return back()->with('error', 'No tienes permiso para modificar esta reserva.');
    }

    // Actualizar el estado de la reserva
    $reservation->update(['status' => $request->status]);

    return back()->with('success', 'Estado de la reserva actualizado correctamente.');
}
}

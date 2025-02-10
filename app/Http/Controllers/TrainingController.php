<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Training;
use App\Models\Activity;
use App\Models\Park;
use App\Models\TrainingPhoto;
use App\Models\TrainingSchedule;
use App\Models\TrainingPrice;
use App\Models\TrainingStatus;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Models\TrainingReservation;


class TrainingController extends Controller
{
  
    public function create(Request $request)
    {
        $selectedParkId = $request->query('park_id'); // Obtén el parque seleccionado
        $parks = Auth::user()->parks; // Obtén todos los parques del entrenador
        $activities = Activity::all(); // Todas las actividades disponibles
        
        return view('trainer.training.create', compact('parks', 'selectedParkId', 'activities',));
    }
    public function store(Request $request)
    {
        if (!Auth::user()->medical_fit) {
            return redirect()->back()->with('error', 'Debes subir un apto médico antes de crear un entrenamiento.');
        }
        // Normalizar horarios al formato H:i
        if ($request->has('schedule.start_time')) {
            $startTimes = array_map(fn($time) => date('H:i', strtotime($time)), $request->schedule['start_time']);
            $request->merge(['schedule.start_time' => $startTimes]);
        }

        if ($request->has('schedule.end_time')) {
            $endTimes = array_map(fn($time) => date('H:i', strtotime($time)), $request->schedule['end_time']);
            $request->merge(['schedule.end_time' => $endTimes]);
        }
        $totalDays = 0;
        foreach ($request->schedule['days'] as $days) {
            $totalDays += count($days);
        }
        // Validar los datos de entrada
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'park_id' => 'required|exists:parks,id',
            'activity_id' => 'required|exists:activities,id',
            'level' => 'required|in:Principiante,Intermedio,Avanzado',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg', // Validar imágenes
            'photos_description.*' => 'nullable|string|max:255', // Validar descripciones de las fotos
            'schedule.days' => 'required|array',
            'schedule.days.*' => 'required|array', // Cada bloque de días debe ser un array
            'schedule.start_time.*' => 'required|string', // Relajar para cualquier string
            'schedule.end_time.*' => 'required|string|after:schedule.start_time.*',
            'prices.weekly_sessions' => 'required|array',
            'prices.price' => 'required|array',
            'available_spots' => 'required|integer|min:1', // Validar cupos disponibles como un entero mínimo de 1
        ]);

    
        $existingStartTimes = [];
        foreach ($request->schedule['days'] as $index => $days) {
            foreach ($days as $day) {
            $key = $day . '-' . $request->schedule['start_time'][$index];

            if (in_array($key, $existingStartTimes)) {
                return redirect()->back()->with('error', "No puedes agregar el mismo día con el mismo horario de inicio más de una vez ($day a las {$request->schedule['start_time'][$index]}).");
            }
            $existingStartTimes[] = $key;
        }
        }
        // Validar que no se repitan valores en "veces por semana"
        $weeklySessions = $request->prices['weekly_sessions'];
        if (count($weeklySessions) !== count(array_unique($weeklySessions))) {
            return redirect()->back()->with('error', 'No puedes agregar más de un precio con la misma cantidad de sesiones por semana.');
        }
        //Validaridar que ningún número en "veces por semana" supere los días de entrenamiento
        foreach ($request->prices['weekly_sessions'] as $sessions) {
            if ($sessions > $totalDays) {
                return redirect()->back()->with('error', 'No puedes ofrecer más sesiones por semana en los precios que la cantidad de clases ofrecidas.');
            }
        }
        // Crear el entrenamiento
        $training = Training::create([
            'trainer_id' => Auth::id(),
            'park_id' => $request->park_id,
            'activity_id' => $request->activity_id,
            'title' => $request->title,
            'description' => $request->description,
            'level' => $request->level,
            'available_spots' => $request->available_spots,
        ]);

        // Manejar la subida de imágenes
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $imagePath = 'training_photos/' . uniqid() . '.' . $photo->getClientOriginalExtension();
        
                // Redimensionar la imagen
                $resizedImage = Image::make($photo)->resize(800, 600, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
        
                // Guardar solo la imagen redimensionada
                $resizedImage->save(storage_path('app/public/' . $imagePath));
        
                // Registrar en la base de datos
                TrainingPhoto::create([
                    'training_id' => $training->id,
                    'photo_path' => $imagePath,
                    'training_photos_description' => $request->photos_description, // Usar la descripción del campo hidden
                ]);
            }
        }
        // Guardar horarios
        foreach ($request->schedule['days'] as $index => $days) {
            foreach ($days as $day) {
                TrainingSchedule::create([
                    'training_id' => $training->id,
                    'day' => $day,
                    'start_time' => $request->schedule['start_time'][$index],
                    'end_time' => $request->schedule['end_time'][$index],
                ]);
            }
        }

        // Guardar precios
        foreach ($request->prices['weekly_sessions'] as $index => $sessions) {
            TrainingPrice::create([
                'training_id' => $training->id,
                'weekly_sessions' => $sessions,
                'price' => $request->prices['price'][$index],
            ]);
        }

        return redirect()->route('trainer.calendar')->with('success', 'Entrenamiento creado exitosamente.');
    }

    public function show(Request $request, $id)
    {   
        $selectedDate = $request->query('date'); 
        $selectedTime = $request->query('time');
    
        $training = Training::with([
            'trainer',
            'park',
            'activity',
            'schedules',
            'prices',
            'students',
            'reservations.user' 
        ])->findOrFail($id);
    
        $training->refresh();
    
        $selectedDay = $request->query('day');
        $filteredSchedules = $training->schedules;
    
        if ($selectedDay) {
            $filteredSchedules = $filteredSchedules->filter(fn($schedule) => $schedule->day === $selectedDay);
        }
    
        if ($selectedDate) {
            $filteredSchedules = $filteredSchedules->filter(function ($schedule) use ($selectedDate) {
                return !TrainingStatus::where('training_schedule_id', $schedule->id)
                    ->where('date', $selectedDate)
                    ->where('status', 'suspended')
                    ->exists();
            });
        }
    
        $filteredReservations = $selectedDate
            ? $training->reservations->where('date', $selectedDate)->groupBy('time') 
            : collect([]);
    
        if ($selectedTime) {
            $filteredSchedules = $filteredSchedules->filter(fn($schedule) => $schedule->start_time == $selectedTime);
        }
    
        // 📌 **Construir URL para tomar lista**
        $reservationDetailUrl = route('trainings.reservation-detail', [
            'id' => $training->id,
            'date' => $selectedDate,
            'time' => $selectedTime
        ]);
    
        // 📌 **Determinar si el entrenador puede tomar lista**
        $isClassAccessible = false;
        $accessMessage = '';
    
        if ($selectedDate && $selectedTime) {
            $classStartTime = Carbon::parse("$selectedDate $selectedTime");
            $classEndTime = $classStartTime->copy()->addHours(24);
            $now = now();
    
            if ($now->lessThan($classStartTime)) {
                $accessMessage = "Disponible desde " . $classStartTime->format('H:i');
            } elseif ($now->greaterThanOrEqualTo($classStartTime) && $now->lessThanOrEqualTo($classEndTime)) {
                $isClassAccessible = true;
            } else {
                $accessMessage = "Acceso cerrado";
            }
        }
    
        $role = auth()->user()->role;
        $view = ($role === 'entrenador' || $role === 'admin') ? 'trainer.training.show' : 'student.show-training';
    
        return view($view, compact(
            'training', 'filteredSchedules', 'selectedDay', 'selectedTime', 'selectedDate', 'filteredReservations',
            'isClassAccessible', 'accessMessage', 'reservationDetailUrl' // 📌 Se pasa la URL como variable
        ));
    }
    ///Mis entrenamientos del entrenador
    public function showAll($id)
    {
        $training = Training::with(['schedules', 'photos', 'students'])->findOrFail($id);
        return view('trainings.show', compact('training'));
    }
    public function edit(Request $request, $id)
    {
        $training = Training::with(['trainer', 'park', 'activity', 'schedules', 'prices'])->findOrFail($id);

    

        $selectedDay = ucfirst(strtolower($request->query('day')));

        if ($training->schedules->isNotEmpty()) {
            $filteredSchedules = $selectedDay
                ? $training->schedules->filter(fn($schedule) => strtolower($schedule->day) === strtolower($selectedDay))
                : $training->schedules;
        } else {
            $filteredSchedules = collect();
        }

        $activities = Activity::all();
        $parks = Park::all();

        return view('trainer.training.edit', compact('training', 'activities', 'parks', 'filteredSchedules', 'selectedDay'));
    }

    public function update(Request $request, $id)
    {
        // Normalizar horarios al formato H:i
        if ($request->has('schedule.start_time')) {
            $startTimes = array_map(fn($time) => date('H:i', strtotime($time)), $request->schedule['start_time']);
            $request->merge(['schedule.start_time' => $startTimes]);
        }

        if ($request->has('schedule.end_time')) {
            $endTimes = array_map(fn($time) => date('H:i', strtotime($time)), $request->schedule['end_time']);
            $request->merge(['schedule.end_time' => $endTimes]);
        }

        // Validar los datos principales del entrenamiento
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|in:Principiante,Intermedio,Avanzado',
            'activity_id' => 'required|exists:activities,id',
            'park_id' => 'required|exists:parks,id',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg',
            'photos_description.*' => 'nullable|string|max:255',
            'available_spots' => 'required|integer|min:1', // Validar cupos disponibles como un entero mínimo de 1
            
        ]);

        // Validar los horarios (schedules) y precios
        $request->validate([
            'schedule.days.*' => 'nullable|array',
            'schedule.start_time.*' => 'required|string', // Relajar para cualquier string
            'schedule.end_time.*' => 'required|string|after:schedule.start_time.*',
            'prices.weekly_sessions.*' => 'nullable|integer|min:1',
            'prices.price.*' => 'nullable|numeric|min:0',
        ]);

        // Buscar el entrenamiento
        $training = Training::findOrFail($id);

        // Verificar permisos (opcional)
        if (auth()->id() !== $training->trainer_id) {
            abort(403, 'No tienes permiso para editar este entrenamiento.');
        }

        // Actualizar los datos principales
        $training->update($validated);

        // Actualizar horarios
        $training->schedules()->delete(); // Borrar horarios existentes
        if ($request->has('schedule.days')) {
            foreach ($request->schedule['days'] as $index => $days) {
                foreach ($days as $day) {
                    $training->schedules()->create([
                        'day' => $day,
                        'start_time' => $request->schedule['start_time'][$index],
                        'end_time' => $request->schedule['end_time'][$index],
                    ]);
                }
            }
        }

        // Actualizar precios
        $training->prices()->delete(); // Borrar precios existentes
        if ($request->has('prices.weekly_sessions')) {
            foreach ($request->prices['weekly_sessions'] as $index => $weekly_sessions) {
                $training->prices()->create([
                    'weekly_sessions' => $weekly_sessions,
                    'price' => $request->prices['price'][$index],
                ]);
            }
        }
        // Manejar la subida de nuevas imágenes
        if ($request->hasFile('photos')) {
            // Eliminar las fotos existentes asociadas al entrenamiento
            foreach ($training->photos as $existingPhoto) {
                if (\Storage::disk('public')->exists($existingPhoto->photo_path)) {
                    \Storage::disk('public')->delete($existingPhoto->photo_path); // Eliminar la foto del disco
                }
                $existingPhoto->delete(); // Eliminar el registro de la base de datos
            }
        
            // Manejar la nueva foto
            foreach ($request->file('photos') as $photo) {
                $imagePath = 'training_photos/' . uniqid() . '.' . $photo->getClientOriginalExtension();
        
                // Redimensionar la imagen
                $resizedImage = Image::make($photo)->resize(800, 600, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
        
                // Guardar solo la imagen redimensionada
                $resizedImage->save(storage_path('app/public/' . $imagePath));
        
                // Registrar en la base de datos
                TrainingPhoto::create([
                    'training_id' => $training->id,
                    'photo_path' => $imagePath,
                    'training_photos_description' => $request->photos_description, // Usar la descripción del campo hidden
                ]);
            }
        }
        return redirect()->route('trainer.training.show', $training->id)
                        ->with('success', 'Entrenamiento actualizado con éxito.');
    }

    public function destroy(Request $request, $id)
    {
            $selectedDate = $request->query('date'); 
        $selectedTime = $request->query('time');

        if (!$selectedDate || !$selectedTime) {
            return redirect()->back()->with('error', 'No se especificó una fecha y horario válidos.');
        }

        // 🔍 Buscar la clase (`TrainingSchedule`) exacta en esa fecha y hora
        $schedule = TrainingSchedule::where('training_id', $id)
            ->where('start_time', $selectedTime)
            ->first();

        if (!$schedule) {
            return redirect()->back()->with('error', 'No se encontró ninguna clase para eliminar.');
        }

        // 🔍 Buscar y eliminar las reservas asociadas a esa clase en esa fecha
        $reservations = TrainingReservation::where('training_id', $id)
            ->where('date', $selectedDate)
            ->where('time', $selectedTime)
            ->get();

        if ($reservations->count() > 0) {
            foreach ($reservations as $reservation) {
                $reservation->delete();
            }
        }

        // ✅ Eliminar la clase
        $schedule->delete();

        return redirect()->route('trainer.calendar')->with('success', 'Clase y reservas eliminadas con éxito.');
    }

    public function destroyAll($id)
    {
        // Buscar el entrenamiento
        $training = Training::findOrFail($id);

        // Verificar permisos (opcional)
        if (auth()->id() !== $training->trainer_id) {
            return redirect()->route('trainer.calendar')->with('error', 'No tienes permiso para eliminar este entrenamiento.');
        }
        // Eliminar las fotos asociadas al entrenamiento
        foreach ($training->photos as $photo) {
            $photoPath = $photo->photo_path;

            // Verificar si el archivo existe y eliminarlo
            if (\Storage::disk('public')->exists($photoPath)) {
                \Storage::disk('public')->delete($photoPath);
            }

            // Eliminar el registro de la foto en la base de datos
            $photo->delete();
        }

        // Eliminar el entrenamiento
        $training->delete();

        return redirect()->route('trainer.calendar')->with('success', 'Entrenamiento eliminado con éxito.');
    }

     //Cambia el estado de la clase a suspendida
     public function suspendClass(Request $request)
     {
         \Log::info("🚀 Datos recibidos en suspendClass", [
             'training_id' => $request->training_id,
             'date' => $request->date
         ]);
 
         $trainingId = $request->training_id;
         $trainingDate = $request->date;
 
         // 🚨 Verificar si la fecha es válida
         if (!$trainingDate || !strtotime($trainingDate)) {
             return response()->json(['error' => 'Fecha inválida', 'received' => $trainingDate], 400);
         }
 
         // ✅ Obtener el nombre del día en español a partir de la fecha
         $dayOfWeek = ucfirst(\Carbon\Carbon::parse($trainingDate)->locale('es')->translatedFormat('l'));
 
         \Log::info("📅 Día calculado para la fecha $trainingDate: $dayOfWeek");
 
         // ✅ Buscar el horario (`training_schedule_id`) correspondiente al `training_id` en ese día
         $schedule = TrainingSchedule::where('training_id', $trainingId)
             ->where('day', $dayOfWeek) // Comparar con el nombre del día
             ->first();
 
         if (!$schedule) {
             \Log::error("🚨 No se encontró un training_schedule_id para training_id={$trainingId} en el día {$dayOfWeek}.");
             return response()->json([
                 'error' => 'No se encontró el horario de entrenamiento para la fecha seleccionada',
                 'dayOfWeek' => $dayOfWeek,
                 'training_id' => $trainingId
             ], 400);
         }
 
         // ✅ Guardar en la tabla `training_status`
         TrainingStatus::updateOrCreate(
             [
                 'training_schedule_id' => $schedule->id,
                 'date' => $trainingDate
             ],
             [
                 'status' => 'suspended'
             ]
         );
 
         \Log::info("✅ Clase suspendida con éxito para training_schedule_id={$schedule->id} en fecha {$trainingDate}");
         $deletedReservations = TrainingReservation::where('training_id', $trainingId)
         ->where('date', $trainingDate)
         ->delete();
 
         \Log::info("🗑️ Se eliminaron {$deletedReservations} reservas para training_id={$trainingId} en fecha {$trainingDate}");
 
         return response()->json([
             'message' => 'Clase suspendida con éxito y reservas eliminadas',
             'date' => $trainingDate,
             'deleted_reservations' => $deletedReservations
         ]);
     }

     //Filtra las clases
     public function getTrainingsForWeek(Request $request)
    {
        $weekStartDate = $request->query('week_start_date');

        if (!$weekStartDate || !strtotime($weekStartDate)) {
            return response()->json([
                'error' => 'Fecha de inicio de semana inválida',
                'received' => $weekStartDate,
                'expected_format' => 'YYYY-MM-DD'
            ], 400);
        }

        // Definir los días correctamente (Lunes es 0, Domingo es 6)
        $daysOfWeek = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];

        // 🔹 Asegurar que weekStartDate sea un lunes
        $weekStartDate = date('Y-m-d', strtotime("last Monday", strtotime($weekStartDate . " +1 day")));

        // Obtener entrenamientos programados para la semana
        $trainings = TrainingSchedule::with(['training', 'statuses'])
            ->get()
            ->map(function ($schedule) use ($weekStartDate, $daysOfWeek) {
                // ✅ Obtener el índice correcto del día de la semana
                $dayIndex = array_search($schedule->day, $daysOfWeek);

                if ($dayIndex === false) {
                    return null; // Si el día es inválido, lo ignoramos
                }

                // ✅ Calcular la fecha exacta sumando `dayIndex` al lunes de la semana
                $trainingDate = date('Y-m-d', strtotime("$weekStartDate +$dayIndex days"));

                // 🔹 Verificar si la clase está suspendida en esta fecha
                $isSuspended = TrainingStatus::where('training_schedule_id', $schedule->id)
                    ->where('date', $trainingDate)
                    ->where('status', 'suspended')
                    ->exists();

                if ($isSuspended) {
                    return null; // 🔥 No incluir en la respuesta
                }

                return [
                    'id' => $schedule->id,
                    'training_id' => $schedule->training_id,
                    'date' => $trainingDate, // ✅ Ahora la fecha será correcta
                    'day' => $schedule->day,
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                    'status' => 'active'
                ];
            })
            ->filter() // 🔥 Filtra `null` (clases suspendidas o con error)
            ->values();

        return response()->json($trainings);
    }


    ////////////// POV ALUMNOS 

    public function myTrainings() {
        $userId = Auth::id();
    
        // Obtener los entrenamientos que el alumno ha comprado
        $trainings = Payment::where('user_id', $userId)
            ->with('training')
            ->get()
            ->pluck('training')
            ->unique();
    
        // Obtener solo las reservas activas
        $reservations = TrainingReservation::where('user_id', $userId)
            ->whereIn('status', ['active', 'completed', 'no-show']) // Filtrar por estado
            ->with('training')
            ->orderBy('date', 'asc')
            ->get();
        
        return view('student.training.my-trainings', compact('trainings', 'reservations'));
    }

    public function select(Request $request, $id)
    {
        $user = auth()->user();
        // Obtener el entrenamiento con horarios y precios
        $training = Training::with(['trainer', 'park', 'activity', 'schedules', 'prices', 'reviews.user'])->findOrFail($id);

        // Verificar si el usuario ha comprado este entrenamiento
    $hasPurchased = false;
    if ($user) {
        $hasPurchased = \App\Models\Payment::where('user_id', $user->id)
            ->where('training_id', $training->id)
            ->exists();
    }
        // Validar si se está enviando un día (por ejemplo, 'Lunes')
        $selectedDay = $request->query('day'); // Día seleccionado desde la URL
        
        // Filtrar horarios por el día seleccionado
        $filteredSchedules = $selectedDay
            ? $training->schedules->filter(function ($schedule) use ($selectedDay) {
                return $schedule->day === $selectedDay; // Devuelve solo los horarios del día
            })
            : $training->schedules; // Todos los horarios si no se especifica el día

        // Determinar la vista según el rol del usuario
        $role = auth()->user()->role;

        if ($role === 'entrenador' || $role === 'admin') {
            // Si es entrenador o admin, mostrar la vista del entrenador
            return view('trainer.show', compact('training', 'filteredSchedules', 'selectedDay'));
        } else {
            // Si es alumno, mostrar la vista del alumno
            return view('student.training.show-training', compact('training', 'filteredSchedules', 'selectedDay','hasPurchased'));
        }
    }
    public function showTrainings(Request $request, $parkId, $activityId)
    {
        // Buscar el parque y la actividad
        $park = Park::findOrFail($parkId);
        $activity = Activity::findOrFail($activityId);
    
        // Obtener los filtros del request
        $selectedDays = $request->input('day') ? explode(',', $request->input('day')) : [];
        $selectedHours = $request->input('start_time') ? explode(',', $request->input('start_time')) : [];
        $selectedLevels = $request->input('level') ? explode(',', $request->input('level')) : []; // 🔹 Filtro de nivel
    
        // Filtrar entrenamientos
        $query = Training::where('park_id', $park->id)
            ->where('activity_id', $activityId)
            ->with(['trainer', 'activity', 'schedules']);
    
        // Aplicar filtro de días
        if (!empty($selectedDays)) {
            $query->whereHas('schedules', function ($q) use ($selectedDays) {
                $q->whereIn('day', $selectedDays);
            });
        }
    
        // Aplicar filtro de horarios (permitiendo que los entrenamientos comiencen en la hora seleccionada o dentro de los siguientes 59 minutos)
        if (!empty($selectedHours)) {
            $query->whereHas('schedules', function ($q) use ($selectedHours) {
                $q->where(function ($subQuery) use ($selectedHours) {
                    foreach ($selectedHours as $hour) {
                        $startRange = date('H:i:s', strtotime($hour)); // Hora seleccionada
                        $endRange = date('H:i:s', strtotime($hour . ' +59 minutes')); // Hasta 59 min después
                        $subQuery->orWhereBetween('start_time', [$startRange, $endRange]);
                    }
                });
            });
        }
    
        // 🔹 Aplicar filtro de nivel
        if (!empty($selectedLevels)) {
            $query->whereIn('level', $selectedLevels);
        }
    
        // Obtener entrenamientos filtrados
        $trainings = $query->get();
    
        // Lista de días de la semana y niveles
        $daysOfWeek = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        $levels = ['Principiante', 'Intermedio', 'Avanzado']; // 🔹 Niveles disponibles
    
        return view('parks.trainings', compact('park', 'activity', 'trainings', 'daysOfWeek', 'levels', 'selectedDays', 'selectedHours', 'selectedLevels'));
    }





    /////API
    public function getTrainingsByPark(Request $request)
    {
        $request->validate([
            'park_id' => 'nullable|exists:parks,id', // Hacer park_id opcional
        ]);

        // Base de la consulta: entrenamientos del entrenador autenticado
        $query = Training::with(['schedules', 'activity', 'prices'])
            ->where('trainer_id', Auth::id());

        // Filtrar por parque si park_id está presente
        if ($request->park_id) {
            $query->where('park_id', $request->park_id);
        }

        // Ejecutar la consulta y ordenar por fecha de creación
        $trainings = $query->orderBy('created_at', 'desc')->get();

        return response()->json($trainings);
    }

    





}



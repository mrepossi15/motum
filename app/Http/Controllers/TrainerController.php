<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\HandlesImages;
use App\Models\User;
use App\Models\Training;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use App\Models\Park;
use App\Models\UserExperience;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;



class TrainerController extends Controller
{
    use HandlesImages;

    //////REGISTRO ENTRENADOR
    public function registerTrainer()
    {
        return view('auth.register-trainer');
    }

    public function storeTrainer(Request $request)
    {
     
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'mercado_pago_email' => 'required|email|unique:users',
            'collector_id' => 'required|string|max:255|unique:users', // Validar Collector ID
            'password' => 'required|min:6|confirmed',
            'certification' => 'required|string|max:255',
            'biography' => 'nullable|string|max:500',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'park_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'opening_hours' => 'nullable|string',
            'especialty' => 'nullable|string|max:255',
            'birth' => 'required|date|before_or_equal:' . Carbon::now()->subYears(18)->format('Y-m-d'),
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg',
            'certification_pic' => 'nullable|image|mimes:jpeg,png,jpg',
            'experiences' => 'nullable|array',
            'experiences.*.role' => 'nullable|string|max:255',
            'experiences.*.company' => 'nullable|string|max:255',
            'experiences.*.year_start' => 'nullable|integer|min:1900|max:' . now()->year,
            'experiences.*.year_end' => 'nullable|integer|min:1900|max:' . now()->year,
           'experiences.*.currently_working' => 'nullable|boolean',
           'medical_fit' => 'nullable|image|mimes:jpeg,png,jpg',
           'photo_reference' => 'nullable|array',
           
        ]);
        // dd('Validación realizada correctamente');
        $userData = $request->only([
            'name', 'email',  'mercado_pago_email','password', 'certification', 'biography', 'especialty', 'birth','collector_id'
        ]);
        $userData['role'] = 'entrenador';
        $userData['password'] = Hash::make($request->password);
    
        if ($request->hasFile('profile_pic')) {
            $userData['profile_pic'] = $this->resizeAndSaveImage($request->file('profile_pic'), 'profile_pics', 300, 300);
            $userData['profile_pic_description'] = 'Foto de portada del entrenador ' . $request->name;
        }
        if ($request->hasFile('medical_fit')) {
            $userData['medical_fit'] = $this->resizeAndSaveImage($request->file('medical_fit'), 'medical_fits', 300, 300);
            $userData['medical_fit_description'] = 'Foto de portada del entrenador ' . $request->name;
        }
       
     // Convertir `photo_references` de JSON a array
    $photoReferences = json_decode($request->photo_references, true);

    if (!is_array($photoReferences)) {
        $photoReferences = [];
    }

    // Descargar y guardar hasta 4 fotos
    $photoUrls = [];
    foreach (array_slice($photoReferences, 0, 4) as $photoReference) {
        try {
            // Descargar la imagen desde la URL proporcionada
            $imageContents = Http::get($photoReference)->body();

            // Generar un nombre único para la imagen
            $imageName = 'parks/' . uniqid() . '.jpg';

            // Guardar la imagen en storage/app/public/parks
            Storage::disk('public')->put($imageName, $imageContents);

            // Guardar la ruta pública
            $photoUrls[] = Storage::url($imageName);
        } catch (\Exception $e) {
            \Log::error("❌ Error al guardar imagen: " . $e->getMessage());
        }
    }

    
        // Guardar parque con fotos en la base de datos
        $park = Park::firstOrCreate(
            ['name' => $request->park_name],
            [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'location' => $request->location,
                'opening_hours' => $request->opening_hours,
                'photo_urls' => json_encode($photoUrls), // Guardar como JSON
            ]
        );
    
    
        $user = User::create($userData);
        

        Auth::login($user);
    
        $user->parks()->attach($park->id);
        if ($request->has('experiences')) {
            foreach ($request->experiences as $experience) {
                $user->experiences()->create([
                    'role' => $experience['role'],
                    'company' => $experience['company'] ?? null,
                    'year_start' => $experience['year_start'],
                    'year_end' => $experience['currently_working'] ? null : $experience['year_end'],
                    'currently_working' => $experience['currently_working'] ?? false,
                ]);
            }
        }
    
        return redirect('/trainer/calendar')->with('success', 'Entrenador registrado exitosamente.');
    }
    
    public function editTrainerProfile()
    {
        $trainer = auth()->user(); // Obtener al entrenador autenticado
        return view('trainer.edit-profile', compact('trainer')); // Retornar la vista del formulario
    }
    public function showTrainerProfile()
    {
        // Usuario autenticado
        $trainer = auth()->user();
    
        // Parques asociados al entrenador
        $parks = $trainer->parks()->get();
        
    
        // Entrenamientos asociados al entrenador
        $trainings = $trainer->trainings()->with(['park', 'activity', 'schedules'])->get();
    
        // Fotos asociadas a los entrenamientos del entrenador
        $trainingPhotos = $trainer->trainingPhotos;
    
        return view('trainer.profile', compact('trainer', 'parks', 'trainings', 'trainingPhotos'));
    }

    public function updateTrainer(Request $request)
    {
      
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'mercado_pago_email' => 'nullable|email|unique:users',
            'collector_id' => 'nullable|string|max:255|unique:users', // Validar Collector ID
            'certification' => 'nullable|string|max:255',
            'certification_pic' => 'nullable|image|mimes:jpeg,png,jpg', // Validar imagen
            'biography' => 'nullable|string|max:500',
            'especialty' => 'nullable|string|max:255', // Validar especialidad
            'birth' => 'nullable|date', // Validar fecha de nacimiento
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg', // Validar imagen
            'experiences' => 'nullable|array',
            'experiences.*.role' => 'required|string|max:255',
            'experiences.*.company' => 'nullable|string|max:255',
            'experiences.*.year_start' => 'required|integer|min:1900|max:' . now()->year,
            'experiences.*.year_end' => 'required|integer|min:1900|max:' . now()->year,
            'experiences.*.details' => 'nullable|string',
            'medical_fit' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);
       

        $user = auth()->user(); // Usuario autenticado

        // Actualizar datos básicos del entrenador
        $user->name = $request->name;
        $user->email = $request->email;
        $user->mercado_pago_email = $request->mercado_pago_email;
        $user->collector_id = $request->collector_id;
        $user->certification = $request->certification ?? $user->certification;
        $user->biography = $request->biography ?? $user->biography;
        $user->especialty = $request->especialty ?? $user->especialty;
        $user->birth = $request->birth ?? $user->birth;
        

        // Manejar la subida de la imagen de perfil si existe
        if ($request->hasFile('profile_pic')) {
            // Eliminar la imagen anterior si existe
            if ($user->profile_pic && Storage::disk('public')->exists($user->profile_pic)) {
                Storage::disk('public')->delete($user->profile_pic);
            }

            // Procesar la nueva imagen
            $image = $request->file('profile_pic');
            $imagePath = 'profile_pics/' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Redimensionar la imagen con Intervention Image
            $resizedImage = Image::make($image)->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio(); // Mantener la relación de aspecto
                $constraint->upsize(); // Evitar agrandar imágenes más pequeñas
            });

            // Guardar la imagen redimensionada
            $resizedImage->save(storage_path('app/public/' . $imagePath));

            // Actualizar los datos en la base de datos
            $user->profile_pic = $imagePath;
            $user->profile_pic_description = 'Foto de portada del entrenador ' . $user->name;
        }
        if ($request->hasFile('medical_fit')) {
            if ($user->medical_fit && Storage::disk('public')->exists($user->medical_fit)) {
                Storage::disk('public')->delete($user->medical_fit);
            }

            $certImage = $request->file('medical_fit');
            $certImagePath = 'medical_fits/' . uniqid() . '.' . $certImage->getClientOriginalExtension();

            $resizedCertImage = Image::make($certImage)->resize(600, 400, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $resizedCertImage->save(storage_path('app/public/' . $certImagePath));

            $user->medical_fit = $certImagePath;
            $user->medical_fit_description = 'Apto medico de ' . $user->name . ' actualizada';
        }
        if ($request->hasFile('certification_pic')) {
            if ($user->certification_pic && Storage::disk('public')->exists($user->certification_pic)) {
                Storage::disk('public')->delete($user->certification_pic);
            }

            $certImage = $request->file('certification_pic');
            $certImagePath = 'certification_pics/' . uniqid() . '.' . $certImage->getClientOriginalExtension();

            $resizedCertImage = Image::make($certImage)->resize(600, 400, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $resizedCertImage->save(storage_path('app/public/' . $certImagePath));

            $user->medical_fit = $certImagePath;
            $user->medical_fit_description = 'Certificado de ' . $user->name . ' actualizada';
        }
      
       

        // Guardar los cambios del usuario
        $user->save();

        // Actualizar experiencias laborales
        if ($request->has('experiences')) {
            // Eliminar experiencias existentes
            $user->experiences()->delete();

            // Guardar nuevas experiencias
            foreach ($request->experiences as $experience) {
                $user->experiences()->create([
                    'role' => $experience['role'],
                    'company' => $experience['company'] ?? null,
                    'year' => $experience['year'],
                    'details' => $experience['details'] ?? null,
                ]);
            }
        }

        return redirect()->route('trainer.profile')->with('success', 'Perfil actualizado exitosamente.');
    }

    //////// Mis entrenamientos
    public function index()
{
    $trainings = Training::where('trainer_id', Auth::id())
        ->with(['schedules', 'photos', 'park', 'activity'])
        ->get()
        ->map(function ($training) {
            // Contar cuántos usuarios distintos compraron este entrenamiento
            $training->student_count = Payment::where('training_id', $training->id)
                ->distinct('user_id') // Evitar contar múltiples compras del mismo usuario
                ->count('user_id');
            return $training;
        });

    return view('trainer.index', compact('trainings'));
}
    public function showAll(Request $request, $id)
    {
        $training = Training::with(['schedules', 'photos', 'students'])->findOrFail($id);
        
        // Obtener la fecha seleccionada de la URL (si está presente)
        $selectedDate = $request->query('date', null);
    
        return view('trainer.show', compact('training', 'selectedDate'));
    }
    //////// Calendario
    public function calendar(Request $request)
    {
        $user = auth()->user();

        // Verifica si el usuario tiene parques asociados
        $parks = $user->parks;

        if ($parks->isEmpty()) {
            return redirect()->route('trainer.calendar')->with('error', 'No tienes parques asociados.');
        }

        // Lógica del calendario
        $startOfWeek = Carbon::now()->startOfWeek();
        $groupedTrainings = []; // Carga aquí los entrenamientos agrupados si es necesario

        return view('trainer.calendar', compact('user', 'startOfWeek', 'groupedTrainings', 'parks'));
    }
    
}

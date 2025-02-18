<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Park;
use App\Models\Payment;
use App\Models\Training;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Traits\HandlesImages;
use App\Models\TrainingSchedule;
use App\Models\TrainingReservation;
use Illuminate\Support\Facades\Log;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;


class StudentController extends Controller
{
    use HandlesImages;
 
    public function registerStudent()
    {
        return view('auth.register-student');
    }

    public function storeStudent(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg', // Validar imagen
            'birth' => 'date', // Fecha de nacimiento
            'medical_fit' => 'nullable|image|mimes:jpeg,png,jpg',
            
            
        ]);
    
        $input = $request->all();
    
        // Manejar la subida de la imagen de perfil
        if ($request->hasFile('profile_pic')) {
            $userData['profile_pic'] = $this->resizeAndSaveImage($request->file('profile_pic'), 'profile_pics', 300, 300);
            $userData['profile_pic_description'] = 'Foto de portada del entrenador ' . $request->name;
        }
        if ($request->hasFile('medical_fit')) {
            $userData['medical_fit'] = $this->resizeAndSaveImage($request->file('medical_fit'), 'medical_fits', 300, 300);
            $userData['medical_fit_description'] = 'Foto de portada del entrenador ' . $request->name;
        }
    
        // Crear el usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'alumno', // Rol automático para alumnos
            'profile_pic' => $input['profile_pic'] ?? null,
            'profile_pic_description' => $input['profile_pic_description'] ?? null,
            'birth' => $request->birth ?? null,
            'medical_fit' => 'nullable|image|mimes:jpeg,png,jpg',
            'medical_fit_description' => $input['medical_fit_description'] ?? null,
        ]);
        Mail::to($user->email)->send(new WelcomeMail($user));
    
        // Iniciar sesión automáticamente
        Auth::login($user);
    
        return redirect('/mapa')->with('success', 'Alumno registrado exitosamente.');
    }
    public function studentProfile($id)
    {
        $user = User::findOrFail($id); // Buscar el usuario por ID
        return view('student.show-profile', compact('user'));
    }
    

    public function editStudentProfile()
    {
        $user = Auth::user(); // Obtener el usuario autenticado
        return view('student.edit-profile', compact('user'));
    }

    public function updateStudent(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'biography' => 'nullable|string|max:1000',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg',
            'birth' => 'date',
            'medical_fit' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);

        $user = Auth::user();
        $user->fill($request->only(['name', 'email', 'birth', 'biography']));
 
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


        $user->save();

        return redirect()->route('student.profile')->with('success', 'Perfil actualizado correctamente.');
    }

    public function myTrainings() {
        $userId = Auth::id();
    
        // Obtener los entrenamientos que el alumno ha comprado
        $trainings = Payment::where('user_id', $userId)
            ->with('training')
            ->get()
            ->pluck('training')
            ->unique();
    
        // Obtener las reservas activas del usuario
        $reservations = TrainingReservation::where('user_id', $userId)
            ->with('training')
            ->orderBy('date', 'asc')
            ->get();
    
        return view('student.training.my-trainings', compact('trainings', 'reservations'));
    }


}

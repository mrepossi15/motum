@extends('layouts.main')

@section('title', 'Registro de Alumno')

@section('content')
<div class="container mt-5 d-flex justify-content-center align-items-center">
    <div class="card shadow-sm p-4" style="width: 100%; max-width: 500px;">
        <h1 class="text-center mb-4 text-naranja">Registro de Alumno</h1>

        <!-- Agregar enctype para manejar la subida de archivos -->
        <form action="{{ route('store.student') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="role" value="alumno">
            <div class="mb-3">
                <label for="name" class="form-label">Nombre completo *</label>
                <input type="text" 
                       class="form-control @error('name') is-invalid @enderror"  
                       id="name" 
                       name="name" 
                       placeholder="Tu nombre completo" 
                       value="{{ old('name') }}"  
                       >
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Correo electrónico -->
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico *</label>
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror"  
                       id="email" 
                       name="email" 
                       placeholder="ejemplo@correo.com" 
                       value="{{ old('email') }}"  
                        >
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Contraseña -->
            <div class="mb-3 position-relative">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       id="password" 
                       name="password" 
                       placeholder="Crea una contraseña" 
                        >
                <button type="button" 
                        class="btn btn-link position-absolute end-0 top-0 mt-3 me-2 toggle-password" 
                        data-target="password" 
                        aria-label="Mostrar/Ocultar contraseña">
                    <i class="bi bi-eye-slash" id="toggle-password-icon"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirmar Contraseña -->
            <div class="mb-3 position-relative">
                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                <input type="password" 
                       class="form-control @error('password_confirmation') is-invalid @enderror" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       placeholder="Repite tu contraseña" 
                        >
                <button type="button" 
                        class="btn btn-link position-absolute end-0 top-0 mt-3 me-2 toggle-password" 
                        data-target="password_confirmation" 
                        aria-label="Mostrar/Ocultar contraseña">
                    <i class="bi bi-eye-slash" id="toggle-password-confirmation-icon"></i>
                </button>
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <input type="hidden" name="role" value="alumno">

            
            <div class="mb-3">
                <label for="birth" class="form-label">Fecha de Nacimiento *</label>
                <input type="date" 
                       id="birth" 
                       name="birth" 
                       class="form-control @error('birth') is-invalid @enderror" 
                       value="{{ old('birth') }}" 
                        >
                @error('birth')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Foto de Perfil -->
            <div class="mb-3">
                <label for="profile_pic" class="form-label">Foto de perfil</label>
                <input type="file" 
                       class="form-control @error('profile_pic') is-invalid @enderror"  
                       id="profile_pic" 
                       name="profile_pic" 
                       accept="image/*">
                @error('profile_pic')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <!-- Biografía -->
            <div class="mb-3">
                <label for="biography" class="form-label">Breve biografía (Opcional)</label>
                <textarea class="form-control @error('biography') is-invalid @enderror" 
                          id="biography" 
                          name="biography" 
                          placeholder="Escribe una breve biografía (máximo 500 caracteres)" 
                          maxlength="500">{{ old('biography') }}</textarea>
                @error('biography')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <!-- Apto medico -->
            <div class="mb-3">
                <label for="medical_fit" class="form-label">Apto médico</label>
                <input type="file" 
                       class="form-control @error('medical_fit') is-invalid @enderror"  
                       id="medical_fit" 
                       name="medical_fit" 
                       accept="image/*">
                @error('medical_fit')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
           

            <div class="d-grid">
                <button type="submit" class="btn text-white bg-naranja btn-block">Registrar como Alumno</button>
            </div>
        </form>

        <div class="text-center mt-3">
            <p>¿Eres entrenador? <a href="{{ route('register.trainer') }}" class="text-naranja">Regístrate aquí</a></p>
        </div>
    </div>
</div>
<script>
    document.querySelectorAll('.toggle-password').forEach(button => {
    const input = document.getElementById(button.getAttribute('data-target'));

    // Evento para alternar la visibilidad de la contraseña al hacer clic en el botón
    button.addEventListener('click', function (event) {
        event.preventDefault(); // Evitar comportamiento por defecto del botón
        const icon = this.querySelector('i');

        // Cambiar entre texto y contraseña
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }

        // Mantener el foco en el campo
        input.focus();
    });

    // Evento para volver a encriptar al perder el foco
    input.addEventListener('blur', function () {
        if (input.type === 'text') {
            input.type = 'password';
            const icon = button.querySelector('i');
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }
    });
});
</script>
@endsection
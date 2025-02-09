@extends('layouts.main')

@section('title', 'Login')

@section('content')
<div class="container mt-5 d-flex justify-content-center align-items-center ">
    <div class="card shadow-sm p-4" style="width: 100%; max-width: 400px; ">
        <h1 class="text-center mb-4 text-naranja">Iniciar Sesión</h1>
        @if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif

        <form action="/login" method="POST">
    @csrf

    <div class="mb-3">
        <label for="email" class="form-label">Correo Electrónico *</label>
        <input type="email" 
               class="form-control @error('email') is-invalid @enderror"  
               id="email" 
               name="email" 
               placeholder="ejemplo@correo.com" 
               value="{{ old('email') }}">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3 position-relative">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" 
               class="form-control @error('password') is-invalid @enderror" 
               id="password" 
               name="password" 
               placeholder="Escribí tu contraseña">
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
    <div class="text-center my-3 d-none" id="loading-spinner">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
    </div>
</div>
    <div class="d-grid">
        <button type="submit" class="btn bg-naranja text-white btn-block">Iniciar Sesión</button>
    </div>
</form>

        <div class="text-center mt-3">
        <a href="{{ route('password.request') }}" class="text-muted">¿Olvidaste tu contraseña?</a>
            <div class="text-center mt-3">
            <p>¿Eres nuevo? <a href="{{ route('register.student') }}" class="text-naranja">Regístrate aquí</a></p>
            </div>
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
document.querySelector('form').addEventListener('submit', function (event) {
        // Mostrar el spinner
        const spinner = document.getElementById('loading-spinner');
        spinner.classList.remove('d-none');

        // Deshabilitar el botón de envío
        const submitButton = event.target.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Cargando...';
    });

    // Lógica para alternar la visibilidad de la contraseña (se mantiene igual)
    document.querySelectorAll('.toggle-password').forEach(button => {
        const input = document.getElementById(button.getAttribute('data-target'));

        button.addEventListener('click', function (event) {
            event.preventDefault();
            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }

            input.focus();
        });

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


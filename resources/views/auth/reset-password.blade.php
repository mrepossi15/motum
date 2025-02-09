@extends('layouts.main')

@section('title', 'Restablecer Contraseña')

@section('content')
<div class="container mt-5 d-flex justify-content-center align-items-center">
    <div class="card shadow-sm p-4" style="width: 100%; max-width: 400px;">
        <h1 class="text-center mb-4 text-naranja">Restablecer Contraseña</h1>

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Mostrar el correo electrónico como texto no editable -->
            <div class="mb-3">
                <p class="form-control-plaintext">Correo Electrónico de {{ $email ?? old('email') }}</p>
                <!-- Campo oculto para enviar el correo -->
                <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Nueva Contraseña</label>
                <input type="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       id="password" 
                       name="password" 
                       required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                <input type="password" 
                       class="form-control" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       required>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn bg-naranja text-white btn-block">Restablecer Contraseña</button>
            </div>
        </form>
    </div>
</div>
@endsection
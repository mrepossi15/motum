@extends('layouts.main')

@section('title', 'Restablecer Contraseña')

@section('content')
<div class="container mt-5 d-flex justify-content-center align-items-center">
    <div class="card shadow-sm p-4" style="width: 100%; max-width: 400px;">
        <h1 class="text-center mb-4 text-naranja">Restablecer Contraseña</h1>

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico *</label>
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       id="email" 
                       name="email" 
                       placeholder="Ingresa tu correo electrónico" 
                       value="{{ old('email') }}" 
                       required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="d-grid">
                <button type="submit" class="btn bg-naranja text-white btn-block">Enviar Enlace</button>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.main')

@section('title', 'Registro de Entrenador')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('trainer.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <!-- Foto de Perfil -->
    <div class="mb-3">
        <label for="profile_pic" class="form-label">Foto de Perfil</label>
        <input type="file" class="form-control" id="profile_pic" name="profile_pic" accept="image/*">
    </div>
    <div class="mb-3">
        <label for="name" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ auth()->user()->name }}" required>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Correo Electrónico</label>
        <input type="email" class="form-control" id="email" name="email" value="{{ auth()->user()->email }}" required>
    </div>

    <div class="mb-3">
        <label for="certification" class="form-label">Certificación</label>
        <input type="text" class="form-control" id="certification" name="certification" value="{{ auth()->user()->certification }}">
    </div>

    <div class="mb-3">
        <label for="biography" class="form-label">Biografía</label>
        <textarea class="form-control" id="biography" name="biography" rows="4">{{ auth()->user()->biography }}</textarea>
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

 

    <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
</form>

@endsection
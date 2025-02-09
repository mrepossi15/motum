@extends('layouts.main')

@section('title', 'Editar Perfil')

@section('content')

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-naranja text-white">
            <h2>Editar Perfil</h2>
        </div>
        <div class="card-body">
            <!-- Mostrar mensaje de error -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Formulario para editar el perfil -->
            <form action="{{ route('student.updateProfile') }}" method="POST" enctype="multipart/form-data">>
                @csrf
                @method('PUT')
                <!-- Foto de Perfil -->
                <div class="mb-3">
                    <label for="profile_pic" class="form-label">Foto de Perfil</label>
                    <input type="file" class="form-control" id="profile_pic" name="profile_pic" accept="image/*">
                </div>
                <!-- Nombre -->
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                </div>


                <!-- Biografía -->
                <div class="mb-3">
                    <label for="biography" class="form-label">Biografía</label>
                    <textarea class="form-control" id="biography" name="biography" rows="4">{{ $user->biography }}</textarea>
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

                <!-- Botón Guardar -->
                <button type="submit" class="btn bg-naranja text-white">Guardar Cambios</button>
            </form>
        </div>
        <div class="card-footer text-end">
        <a href="{{ route('student.profile', ['id' => $user->id]) }}" class="btn btn-secondary">Volver</a>
        </div>
    </div>
</div>

@endsection

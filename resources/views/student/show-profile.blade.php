@extends('layouts.main')

@section('title', 'Perfil de Usuario')

@section('content')

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-naranja text-white">
            <h2>Perfil de Usuario</h2>
        </div>
        
        <div class="card-body">
            <p><strong>Nombre:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            
            @if($user->role === 'entrenador')
                <p><strong>Certificación:</strong> {{ $user->certification ?? 'No especificada' }}</p>
            @endif

            <p><strong>Biografía:</strong></p>
            <p>{{ $user->biography ?? 'No especificada' }}</p>

            @if($user->profile_pic)
                <img src="{{ asset('storage/' . $user->profile_pic) }}" alt="Foto de perfil" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
            @endif
        </div>

        <!-- Solo mostrar el botón si el usuario autenticado está viendo su propio perfil -->
        @if(auth()->id() === $user->id)
            <div class="card-footer text-end">
                <a href="{{ route('student.editProfile') }}" class="btn text-white bg-naranja">Editar Perfil</a>
            </div>
        @endif
    </div>
</div>

@endsection

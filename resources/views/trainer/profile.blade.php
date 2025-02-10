@extends('layouts.main')

@section('title', 'Perfil del Entrenador')

@section('content')

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="container mt-5">
    <!-- Información del Entrenador -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-naranja text-white d-flex justify-content-between align-items-center">
            <h2>Información del Entrenador</h2>
            @if(auth()->id() === $trainer->id)  {{-- Solo el entrenador dueño del perfil puede editar --}}
                <a href="{{ route('trainer.editProfile') }}" class="btn btn-light btn-sm">
                    Editar Perfil
                </a>
            @endif
        </div>
        <div class="card-body">
            <p><strong>Nombre:</strong> {{ $trainer->name }}</p>
            <p><strong>Correo Electrónico:</strong> {{ $trainer->email }}</p>
            <p><strong>Certificación:</strong> {{ $trainer->certification ?? 'No especificada' }}</p>
            <p><strong>Biografía:</strong> {{ $trainer->biography ?? 'No especificada' }}</p>
            <div class="mb-3">
                <img src="{{ asset('storage/' . $trainer->profile_pic) }}" alt="Foto de perfil" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
            </div>
        </div>
    </div>

    <!-- Parques Asociados -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-naranja text-white d-flex justify-content-between align-items-center">
            <h2>Parques Asociados</h2>
            @if(auth()->id() === $trainer->id)  {{-- Solo el entrenador dueño del perfil puede agregar parques --}}
                <a href="{{ route('trainer.add.park') }}" class="btn btn-light btn-sm">Agregar Parque</a>
            @endif
        </div>
        <div class="card-body">
            @if ($parks->isEmpty())
                <p class="text-muted">No tienes parques asociados.</p>
            @else
                <ul class="list-group">
                    @foreach ($parks as $park)
                        <li class="list-group-item mb-3">
                            <strong>{{ $park->name }}</strong>
                            <p class="mb-1"><strong>Ubicación:</strong> {{ $park->location }}</p>
                            <p class="mb-1"><strong>Horario:</strong></p>

                            @if ($park->opening_hours)
                                @php
                                    $openingHours = json_decode($park->opening_hours, true);
                                @endphp
                                @if (is_array($openingHours))
                                    <ul>
                                        @foreach ($openingHours as $day => $hours)
                                            <li>{{ $day }}: {{ Str::after($hours, ': ') }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p>{{ $park->opening_hours }}</p> <!-- Mostrar texto plano si no es JSON -->
                                @endif
                            @else
                                <p>No especificado</p>
                            @endif

                            @if(auth()->id() === $trainer->id)  {{-- Solo el entrenador dueño del perfil puede agregar entrenamientos --}}
                                <a href="{{ route('trainer.training.create', ['park_id' => $park->id]) }}" class="btn bg-naranja text-white btn-sm mt-2">Agregar Entrenamiento</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <!-- Fotos de Entrenamientos -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-naranja text-white">
            <h2>Fotos de los Entrenamientos</h2>
        </div>
        <div class="card-body">
            @if ($trainingPhotos->isEmpty())
                <p class="text-muted">No hay fotos de entrenamientos aún.</p>
            @else
                <div class="row">
                    @foreach ($trainingPhotos as $photo)
                        <div class="col-md-3 mb-3">
                            <div class="card">
                                <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Foto de entrenamiento" class="card-img-top img-fluid" style="height: 200px; object-fit: cover;">
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
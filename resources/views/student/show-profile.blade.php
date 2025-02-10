@extends('layouts.main')

@section('title', 'Perfil del Entrenador')

@section('content')

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-naranja text-white d-flex justify-content-between align-items-center">
            <h2>Información del Entrenador</h2>
            
            @if(auth()->check() && auth()->id() === $trainer->id)
                <a href="{{ route('trainer.editProfile') }}" class="btn btn-light btn-sm">Editar Perfil</a>
            @endif
        </div>

        <div class="card-body">
            <p><strong>Nombre:</strong> {{ $trainer->name }}</p>
            <p><strong>Email:</strong> {{ $trainer->email }}</p>
            <p><strong>Certificación:</strong> {{ $trainer->certification ?? 'No especificada' }}</p>
            <p><strong>Biografía:</strong> {{ $trainer->biography ?? 'No especificada' }}</p>

            @if($trainer->profile_pic)
                <div class="mb-3">
                    <img src="{{ asset('storage/' . $trainer->profile_pic) }}" alt="Foto de perfil" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                </div>
            @endif
        </div>
    </div>

    <!-- Parques Asociados -->
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-naranja text-white d-flex justify-content-between align-items-center">
            <h2>Parques Asociados</h2>

            @if(auth()->check() && auth()->id() === $trainer->id)
                <a href="{{ route('trainer.add.park') }}" class="btn btn-light btn-sm">Agregar Parque</a>
            @endif
        </div>
        <div class="card-body">
            @if ($parks->isEmpty())
                <p class="text-muted">No hay parques asociados.</p>
            @else
                <ul class="list-group">
                    @foreach ($parks as $park)
                        <li class="list-group-item">
                            <strong>{{ $park->name }}</strong>
                            <p class="mb-1"><strong>Ubicación:</strong> {{ $park->location }}</p>
                            
                            @if(auth()->check() && auth()->id() === $trainer->id)
                                <a href="{{ route('trainer.training.create', ['park_id' => $park->id]) }}" class="btn bg-naranja text-white btn-sm mt-2">Agregar Entrenamiento</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>

@endsection
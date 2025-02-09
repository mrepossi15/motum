@extends('layouts.main')

@section('title', "Entrenamientos de {$activity->name} en {$park->name}")

@section('content')
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<div class="container mt-5">
    <h1>Entrenamientos de {{ $activity->name }} en {{ $park->name }}</h1>

    @if ($trainings->isEmpty())
        <p>No hay entrenamientos disponibles para esta actividad en este parque.</p>
    @else
        <ul class="list-group">
            @foreach($trainings as $training)
                <li class="list-group-item training-card mb-2">
                    <a href="{{ route('students-trainings.show', $training->id) }}" class="text-decoration-none text-dark">
                        @if ($training->photos->isEmpty())
                            <p class="text-muted">No hay fotos para este entrenamiento.</p>
                        @else
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <div class="card">
                                        <img src="{{ asset('storage/' . $training->photos->first()->photo_path) }}" 
                                             alt="Foto de entrenamiento" 
                                             class="card-img-top img-fluid" 
                                             style="height: 200px; object-fit: cover;">
                                    </div>
                                </div>
                            </div>
                        @endif
                        <h4>{{ $training->title }}</h4>
                        <p>{{ $training->description }}</p>
                        <p>
                            <strong>Nivel:</strong> {{ $training->level }}<br>
                            <strong>Entrenador:</strong> {{ $training->trainer->name ?? 'N/A' }}<br>
                            <strong>Creado el:</strong> {{ $training->created_at->format('d/m/Y') }}
                        </p>
                    </a>

                    <!-- Botón de Favoritos dentro del loop -->
                    <button class="btn btn-outline-danger favorite-btn mt-2" 
                            data-id="{{ $training->id }}" 
                            data-type="training">
                        ❤️ Guardar
                    </button>
                </li>
            @endforeach
        </ul>
    @endif

    <a href="{{ route('parks.show', $park->id) }}" class="btn btn-secondary mt-3">Volver al parque</a>
</div>


@endsection
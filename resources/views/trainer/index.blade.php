@extends('layouts.main')

@section('title', 'Mis Entrenamientos')

@section('content')
<main class="container mt-4">
    <h2 class="mb-4">Mis Entrenamientos</h2>

    @if ($trainings->count() > 0)
        <div class="row">
            @foreach ($trainings as $training)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm">
                        @if ($training->photos->isNotEmpty())
                            <img src="{{ asset('storage/' . $training->photos->first()->photo_path) }}" 
                                 class="card-img-top" 
                                 alt="Foto de entrenamiento" 
                                 style="height: 200px; object-fit: cover;">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $training->title }}</h5>
                            <p class="card-text">
                                <strong>Ubicación:</strong> {{ $training->park->name }} <br>
                                <strong>Actividad:</strong> {{ $training->activity->name }} <br>
                                <strong>Nivel:</strong> {{ $training->level }}
                            </p>
                            <p class="mb-2">
                                <strong>Horarios:</strong>
                                @foreach ($training->schedules as $schedule)
                                    <span class="badge bg-secondary">
                                        {{ $schedule->day }} ({{ $schedule->start_time }} - {{ $schedule->end_time }})
                                    </span>
                                @endforeach
                            </p>
                            <p class="mb-2">
                                <strong>👥 Alumnos inscritos:</strong> 
                                <span class="badge bg-info">{{ $training->student_count }}</span>
                            </p>
                            <a href="{{ route('trainer.showAll', $training->id) }}" class="btn btn-primary w-100">
                                Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-muted">No tienes entrenamientos creados.</p>
    @endif
</main>
@endsection
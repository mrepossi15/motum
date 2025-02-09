@extends('layouts.main')

@section('title', $training->title)

@section('content')
<main class="container mt-4">
    <div class="card shadow-sm">
        @if ($training->photos->isNotEmpty())
            <img src="{{ asset('storage/' . $training->photos->first()->photo_path) }}" 
                 class="card-img-top" 
                 alt="Foto del entrenamiento" 
                 style="height: 250px; object-fit: cover;">
        @endif
        <div class="card-body">
            <h2 class="card-title text-center">{{ $training->title }}</h2>
            <p class="text-muted text-center">{{ $training->park->name }}</p>

            <h4 class="mt-4">📅 Días y Horarios:</h4>
            <div class="d-flex flex-wrap gap-2">
                @foreach($training->schedules as $schedule)
                    <span class="badge bg-secondary">
                        {{ $schedule->day }} ({{ $schedule->start_time }} - {{ $schedule->end_time }})
                    </span>
                @endforeach
            </div>

            <h4 class="mt-4">📸 Fotos</h4>
            <div class="row">
                @foreach($training->photos as $photo)
                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <img src="{{ asset('storage/' . $photo->photo_path) }}" 
                                 alt="Foto de entrenamiento" 
                                 class="card-img-top img-fluid" 
                                 style="height: 150px; object-fit: cover;">
                        </div>
                    </div>
                @endforeach
            </div>

            <h4 class="mt-4">👥 Participantes</h4>
            <ul class="list-group">
                @foreach($training->students as $student)
                    <li class="list-group-item">
                        <a href="{{ route('student.profile', $student->id) }}" class="text-decoration-none fw-bold text-primary">
                            {{ $student->name }}
                        </a> 
                        <span class="text-muted">({{ $student->email }})</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</main>
@endsection
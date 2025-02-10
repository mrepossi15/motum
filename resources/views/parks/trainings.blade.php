@extends('layouts.main')

@section('title', "Entrenamientos de {$activity->name} en {$park->name}")

@section('content')
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<div class="container mt-3">
    <div class="row">
        <!-- Filtro por Díassssssss ok -->
        <div class="col-md-4">
            <strong>Filtrar por Día:</strong><br>
            @foreach($daysOfWeek as $day)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="day[]" value="{{ $day }}" 
                           id="day-{{ $day }}" {{ in_array($day, $selectedDays ?? []) ? 'checked' : '' }}>
                    <label class="form-check-label" for="day-{{ $day }}">{{ $day }}</label>
                </div>
            @endforeach
        </div>

        <!-- Filtro por Hora de Inicio -->
        <div class="col-md-4">
            <strong>Filtrar por Hora:</strong><br>
            @for ($i = 6; $i <= 22; $i++) <!-- Horarios de 6 AM a 10 PM -->
                @php $hourFormatted = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00'; @endphp
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="start_time[]" value="{{ $hourFormatted }}" 
                           id="hour-{{ $hourFormatted }}" {{ in_array($hourFormatted, $selectedHours ?? []) ? 'checked' : '' }}>
                    <label class="form-check-label" for="hour-{{ $hourFormatted }}">{{ $hourFormatted }}</label>
                </div>
            @endfor
        </div>

        <!-- Filtro por Nivel -->
        <div class="col-md-4">
            <strong>Filtrar por Nivel:</strong><br>
            @foreach($levels as $level)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="level[]" value="{{ $level }}" 
                           id="level-{{ $level }}" {{ in_array($level, $selectedLevels ?? []) ? 'checked' : '' }}>
                    <label class="form-check-label" for="level-{{ $level }}">{{ ucfirst($level) }}</label>
                </div>
            @endforeach
        </div>
    </div>
</div>

@if ($activity)
    <h1>Entrenamientos de {{ $activity->name }} en {{ $park->name }}</h1>
@else
    <h1>Entrenamientos en {{ $park->name }}</h1>
@endif

@php
    $dayOrder = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
@endphp

@if ($trainings->isEmpty())
    <p>No hay entrenamientos disponibles en este parque.</p>
@else
    <ul class="list-group">
        @foreach($trainings as $training)
            @php
                // Obtener y ordenar los horarios del entrenamiento
                $schedules = $training->schedules->sortBy(function ($schedule) use ($dayOrder) {
                    return array_search($schedule->day, $dayOrder);
                });
            @endphp
            @if (!$schedules->isEmpty()) 
                <li class="list-group-item training-card mb-2">
                    <a href="{{ route('students-trainings.show', $training->id) }}" class="text-decoration-none text-dark">
                        <h4>{{ $training->title }}</h4>
                        <p>{{ $training->description }}</p>
                        <p>
                            <strong>Nivel:</strong> {{ ucfirst($training->level) }}<br>
                            <strong>Entrenador:</strong> {{ $training->trainer->name ?? 'N/A' }}<br>
                        </p>
                        <p>
                            <strong>Horarios:</strong>
                            <ul>
                                @foreach($schedules as $schedule)
                                    <li>{{ $schedule->day }}: {{ $schedule->start_time }} - {{ $schedule->end_time }}</li>
                                @endforeach
                            </ul>
                        </p>
                    </a>
                </li>
            @endif
        @endforeach
    </ul>
@endif

<a href="{{ route('parks.show', $park->id) }}" class="btn btn-secondary mt-3">Volver al parque</a>

<script>
    function applyFilters() {
        const selectedDays = Array.from(document.querySelectorAll('input[name="day[]"]:checked'))
                                  .map(el => el.value)
                                  .join(',');
        const selectedTimes = Array.from(document.querySelectorAll('input[name="start_time[]"]:checked'))
                                  .map(el => el.value)
                                  .join(',');
        const selectedLevels = Array.from(document.querySelectorAll('input[name="level[]"]:checked'))
                                  .map(el => el.value)
                                  .join(',');

        let url = new URL(window.location.href);

        if (selectedDays) {
            url.searchParams.set('day', selectedDays);
        } else {
            url.searchParams.delete('day');
        }

        if (selectedTimes) {
            url.searchParams.set('start_time', selectedTimes);
        } else {
            url.searchParams.delete('start_time');
        }

        if (selectedLevels) {
            url.searchParams.set('level', selectedLevels);
        } else {
            url.searchParams.delete('level');
        }

        window.location.href = url.toString();
    }

    document.querySelectorAll('input[type="checkbox"]').forEach(el => {
        el.addEventListener('change', applyFilters);
    });
</script>
@endsection
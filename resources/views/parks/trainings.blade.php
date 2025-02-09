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
        <!-- Filtro por Día -->
        <div class="col-md-4">
            <select id="day-filter" class="form-select" name="day" onchange="applyFilters()">
                <option value="">Todos los días</option>
                @foreach($daysOfWeek as $day)
                    <option value="{{ $day }}" {{ $selectedDay == $day ? 'selected' : '' }}>
                        {{ $day }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Filtro por Hora de Inicio -->
        <div class="col-md-4">
            <input type="time" id="time-filter" class="form-control" name="start_time" value="{{ $selectedHour ?? '' }}" onchange="applyFilters()">
        </div>
    </div>
</div>
@if ($activity)
    <h1>Entrenamientos de {{ $activity->name }} en {{ $park->name }}</h1>
@else
    <h1>Entrenamientos en {{ $park->name }}</h1>
@endif

    @if ($trainings->isEmpty())
    <p>No hay entrenamientos disponibles {{ $selectedDay ? 'el ' . $selectedDay : '' }} en este parque.</p>
@else
    <ul class="list-group">
        @foreach($trainings as $training)
            @php
                $schedules = $training->schedules;
            @endphp
            @if (!$schedules->isEmpty()) <!-- Solo mostrar entrenamientos con horarios -->
                <li class="list-group-item training-card mb-2">
                    <a href="{{ route('students-trainings.show', $training->id) }}" class="text-decoration-none text-dark">
                        <h4>{{ $training->title }}</h4>
                        <p>{{ $training->description }}</p>
                        <p>
                            <strong>Nivel:</strong> {{ $training->level }}<br>
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
</div>
<script>
    function applyFilters() {
    const selectedDay = document.getElementById('day-filter').value;
    const selectedTime = document.getElementById('time-filter').value;
    
    let url = new URL(window.location.href);
    if (selectedDay) {
        url.searchParams.set('day', selectedDay);
    } else {
        url.searchParams.delete('day');
    }

    if (selectedTime) {
        url.searchParams.set('start_time', selectedTime);
    } else {
        url.searchParams.delete('start_time');
    }

    window.location.href = url.toString();
}
</script>


@endsection
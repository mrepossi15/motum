@extends('layouts.main')

@section('title', 'Reservar Clase')

@section('content')
<main class="container mt-4">
    <h2>Reservar Clase para {{ $training->title }}</h2>

    <form action="{{ route('store.reservation', $training->id) }}" method="POST">
        @csrf

        <!-- Seleccionar Fecha -->
        <label for="date">Selecciona una fecha:</label>
        <input type="date" name="date" id="date" required class="form-control">

        <!-- Seleccionar Horario -->
        <label for="time">Selecciona un horario:</label>
        <select name="time" id="time" class="form-control" disabled>
            <option value="">Selecciona una fecha primero</option>
        </select>
        <input type="hidden" id="trainingId" value="{{ $training->id }}">

        <button type="submit" class="btn btn-primary mt-3">Reservar</button>
    </form>
</main>

<script>
document.getElementById('date').addEventListener('change', function() {
    let date = this.value;
    let trainingId = document.getElementById('trainingId').value;
    let timeSelect = document.getElementById('time');

    if (!date) {
        timeSelect.innerHTML = '<option value="">Selecciona una fecha primero</option>';
        timeSelect.disabled = true;
        return;
    }

    fetch(`/trainings/${trainingId}/available-times?date=${date}`)
        .then(response => response.json())
        .then(data => {
            timeSelect.innerHTML = ''; // Limpiar opciones previas
            if (data.length > 0) {
                data.forEach(schedule => {
                    let option = document.createElement('option');
                    option.value = schedule.start_time;
                    option.textContent = `${schedule.start_time} - ${schedule.end_time}`;
                    timeSelect.appendChild(option);
                });
                timeSelect.disabled = false;
            } else {
                timeSelect.innerHTML = '<option value="">No hay horarios disponibles para esta fecha</option>';
                timeSelect.disabled = true;
            }
        })
        .catch(error => console.error('Error obteniendo los horarios:', error));
});
</script>

@endsection
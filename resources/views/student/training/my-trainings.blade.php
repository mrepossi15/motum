@extends('layouts.main')

@section('title', 'Mis Entrenamientos')

@section('content')
<main class="container mt-4">
    <h2>Mis Entrenamientos</h2>
    <div class="list-group">
        @forelse($trainings as $training)
            <a href="{{ route('reserve.training.view', $training->id) }}" class="list-group-item list-group-item-action">
                <h5 class="mb-1">{{ $training->title }}</h5>
                <p class="mb-1"><strong>Parque:</strong> {{ $training->park->name }}</p>
                <p class="mb-1"><strong>Actividad:</strong> {{ $training->activity->name }}</p>
                <p class="mb-1">
                    <strong>Cupos Disponibles:</strong> 
                    {{ $training->available_spots - $training->reservations->count() }} / {{ $training->available_spots }}
                </p>
            </a>
        @empty
            <p>No has comprado entrenamientos aún.</p>
        @endforelse
    </div>

    <h2 class="mt-4">Mis Reservas</h2>
@if($reservations->isEmpty())
    <p>No tienes reservas aún.</p>
@else
    <table class="table">
        <tr>
            <th>Entrenamiento</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Cupos Disponibles</th>
            <th>Estado</th>
            <th>Acción</th>
        </tr>
        @foreach($reservations as $reservation)
        <tr>
            <td>{{ $reservation->training->title }}</td>
            <td>{{ $reservation->date }}</td>
            <td>{{ $reservation->time }}</td>
            <td>
                @php
                    $totalReservations = \App\Models\TrainingReservation::where('training_id', $reservation->training->id)
                        ->where('date', $reservation->date)
                        ->where('time', $reservation->time)
                        ->count();
                    $cuposRestantes = $reservation->training->available_spots - $totalReservations;
                @endphp
                {{ $cuposRestantes }} / {{ $reservation->training->available_spots }}
            </td>
            <td>
                @if($reservation->status === 'active')
                    <span class="badge bg-success">Activa</span>
                @elseif($reservation->status === 'completed')
                    <span class="badge bg-primary">Completada</span>
                @elseif($reservation->status === 'no-show')
                    <span class="badge bg-warning">No asistió</span>
                @endif
            </td>
            <td>
                @if($reservation->status === 'active')
                    <form action="{{ route('cancel.reservation', $reservation->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Cancelar</button>
                    </form>
                @else
                    <span class="text-muted">No modificable</span>
                @endif
            </td>
        </tr>
        @endforeach
    </table>
@endif
</main>
@endsection
@extends('layouts.main')

@section('title', 'Lista de Asistencia')

@section('content')
<main class="container mt-4">
    <h2>Lista de Asistencia - {{ $training->title }} ({{ $date }} - {{ $selectedTime }})</h2>

    <table class="table">
        <thead>
            <tr>
                <th>Alumno</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reservations as $reservation)
            <tr>
                <td>{{ $reservation->user->name }} ({{ $reservation->user->email }})</td>
                <td>
                    @if($reservation->status == 'active')
                        <span class="badge bg-warning">Pendiente</span>
                    @elseif($reservation->status == 'completed')
                        <span class="badge bg-success">Asistió</span>
                    @elseif($reservation->status == 'no-show')
                        <span class="badge bg-danger">No Asistió</span>
                    @endif
                </td>
                <td>
                    <!-- ✅ Solo mostrar opciones si la reserva está en "active" o puede modificarse -->
                    <form action="{{ route('reservations.updateStatus', $reservation->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="btn btn-success btn-sm"
                            {{ $reservation->status == 'completed' ? 'disabled' : '' }}>
                            ✔️ Asistió
                        </button>
                    </form>
                    
                    <form action="{{ route('reservations.updateStatus', $reservation->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="no-show">
                        <button type="submit" class="btn btn-danger btn-sm"
                            {{ $reservation->status == 'no-show' ? 'disabled' : '' }}>
                            ❌ No Asistió
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if ($reservations->isEmpty())
        <p class="text-center text-muted">No hay participantes en este horario.</p>
    @endif
</main>
@endsection
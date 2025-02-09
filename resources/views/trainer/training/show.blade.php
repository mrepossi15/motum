@extends('layouts.main')

@section('title', 'Detalle del Entrenamiento')

@section('content')

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<main class="container mt-4">
    <div class="card shadow-sm">
    <div class="card-header bg-naranja text-white d-flex justify-content-between align-items-center">
            <h2>{{ $training->title }}</h2>
            <h2>Entrenamiento para {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l d \d\e F') }}</h2>
            <!-- Botón de menú -->
            <div class="dropdown">
                <button class="btn btn-light btn-sm options-menu" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                <a href="{{ route('trainer.training.edit', ['id' => $training->id, 'day' => $selectedDay ?? '']) }}" class="dropdown-item">Editar</a>
                    <li>
                        <button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">Eliminar</button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <p><strong>Parque:</strong> {{ $training->park->name }}</p>
            <p><strong>Ubicación:</strong> {{ $training->park->location }}</p>
            <p><strong>Actividad:</strong> {{ $training->activity->name }}</p>
            <p><strong>Nivel:</strong> {{ $training->level }}</p>
            <p><strong>Entrenador:</strong> {{ $training->trainer->name }}</p>
            <p><strong>Descripción:</strong> {{ $training->description ?? 'No especificada' }}</p>
            <p><strong>Cupos:</strong> {{ $training->available_spots ?? 'No especificada' }}</p>
            
            <p><strong>Fecha Seleccionada:</strong> {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l d \d\e F Y') }}</p>
            <p><strong>Horario:</strong> {{ $selectedTime }} - {{ $filteredSchedules->first()->end_time ?? 'No disponible' }}</p>

            
            <h5>Precios:</h5>
            <ul>
                @foreach ($training->prices as $price)
                    <li>{{ $price->weekly_sessions }} veces por semana: ${{ number_format($price->price, 2) }}</li>
                @endforeach
            </ul>
       
            <<h3>Participantes para {{ $selectedDate }} ({{ $filteredReservations->count() }}/ {{ $training->available_spots }})</h3>

@if ($filteredReservations->count() > 0)
    <ul>
        @foreach ($filteredReservations as $reservation)
            <li>{{ $reservation->user->name }} ({{ $reservation->user->email }}) - {{ $reservation->time }}</li>
        @endforeach
    </ul>
@else
    <p>No hay participantes para esta clase en esta fecha exacta.</p>
@endif
        </div>
        <div class="card shadow-sm mb-4">
        <div class="card-header bg-naranja text-white">
            <h2>Fotos de tus Entrenamientos</h2>
        </div>
        <div class="card-body">
            @if ($training->photos->isEmpty())
                <p class="text-muted">No tienes fotos de entrenamientos aún.</p>
            @else
                <div class="row">
                    @foreach ($training->photos as $photo)
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
        <div class="card-footer text-end">
            <a href="{{ route('trainer.calendar') }}" class="btn btn-outline-secondary">Volver al calendario</a>
        </div>
    </div>
    <!-- Modal de Confirmación -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="deleteModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas eliminar este entrenamiento? Esta acción no se puede deshacer.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form action="{{ route('trainings.suspend') }}" method="POST" onsubmit="return confirmSuspension()">
    @csrf
    <input type="hidden" name="training_id" value="{{ $training->id }}">
    <input type="hidden" name="date" value="{{ $selectedDate }}"> {{-- Ahora usa la fecha de la URL --}}
    <button type="submit" class="btn btn-warning text-white">Suspender Clase</button>
</form>

<script>
function confirmSuspension() {
    return confirm("¿Estás seguro de que deseas suspender esta clase?");
}
</script>
                </div>
    </div>
</main>
@endsection



@extends('layouts.main')

@section('title', 'Crear Entrenamiento')

@section('content')

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<main class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-naranja text-white">
            <h2>Crear Entrenamiento</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('trainings.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
                <!-- Título -->
                <div class="mb-3">
                    <label for="title" class="form-label">
                        Título
                        @error('title') <span class="text-danger">*{{ $message }}</span> @enderror
                    </label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                           id="title" name="title" value="{{ old('title') }}" placeholder="Ej: Clase de Yoga" >
                </div>
                <div class="mb-3">
                    <label for="park" class="form-label">
                        Parque
                        @error('park_id') <span class="text-danger">*{{ $message }}</span> @enderror
                    </label>
                    
                    <select class="form-select @error('park_id') is-invalid @enderror" id="park" name="park_id">
                        <option value="" disabled {{ old('park_id', $selectedParkId) ? '' : 'selected' }}>Seleccionar parque</option>
                        @foreach ($parks as $park)
                            <option value="{{ $park->id }}" 
                                {{ old('park_id', $selectedParkId) == $park->id ? 'selected' : '' }}>
                                {{ $park->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Actividad -->
                <div class="mb-3">
                    <label for="activity" class="form-label">
                        Tipo de Actividad
                        @error('activity_id') <span class="text-danger">*{{ $message }}</span> @enderror
                    </label>
                    <select class="form-select @error('activity_id') is-invalid @enderror" id="activity" name="activity_id" required>
                        @foreach ($activities as $activity)
                            <option value="{{ $activity->id }}" {{ old('activity_id') == $activity->id ? 'selected' : '' }}>
                                {{ $activity->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Nivel -->
                <div class="mb-3">
                    <label for="level" class="form-label">
                        Nivel
                        @error('level') <span class="text-danger">*{{ $message }}</span> @enderror
                    </label>
                    <select class="form-select @error('level') is-invalid @enderror" id="level" name="level" required>
                        <option value="Principiante" {{ old('level') == 'Principiante' ? 'selected' : '' }}>Principiante</option>
                        <option value="Intermedio" {{ old('level') == 'Intermedio' ? 'selected' : '' }}>Intermedio</option>
                        <option value="Avanzado" {{ old('level') == 'Avanzado' ? 'selected' : '' }}>Avanzado</option>
                    </select>
                </div>
                <!--Descripción  -->
                <div class="mb-3">
                    <label for="description" class="form-label">
                        Descripción 
                        @error('description') <span class="text-danger">*{{ $message }}</span> @enderror
                    </label>
                    
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                        id="description" name="description" 
                        placeholder="Escribe una descripción (opcional)">{{ old('description') }}</textarea>
                </div>

                <!--Días y Horarios  -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Días y Horarios</h5>
                        <button type="button" id="add-schedule" class="btn-sm btn bg-naranja-light">Agregar Día y Horario</button>
                    </div>

                    <div id="schedule-container" class="mt-3">
                        @php
                            // Obtener los datos ingresados en caso de error
                            $schedules = old('schedule.days', [[]]);
                        @endphp

                        @foreach ($schedules as $index => $scheduleDays)
                            <div class="border rounded p-3 mb-3 schedule-item">
                                <label>Días:</label>
                                <div>
                                    @foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $day)
                                        <label class="form-check-label me-2">
                                            <input type="checkbox" class="form-check-input" 
                                                name="schedule[days][{{ $index }}][]" 
                                                value="{{ $day }}"
                                                {{ in_array($day, old("schedule.days.$index", [])) ? 'checked' : '' }}>
                                            {{ $day }}
                                        </label>
                                    @endforeach
                                </div>
                                
                                <label>Hora de Inicio:</label>
                                <input type="time" class="form-control @error("schedule.start_time.$index") is-invalid @enderror" 
                                    name="schedule[start_time][{{ $index }}]" 
                                    value="{{ old("schedule.start_time.$index") }}" required>
                                @error("schedule.start_time.$index") 
                                    <span class="text-danger">*{{ $message }}</span> 
                                @enderror
                                
                                <label>Hora de Fin:</label>
                                <input type="time" class="form-control @error("schedule.end_time.$index") is-invalid @enderror" 
                                    name="schedule[end_time][{{ $index }}]" 
                                    value="{{ old("schedule.end_time.$index") }}" required>
                                @error("schedule.end_time.$index") 
                                    <span class="text-danger">*{{ $message }}</span> 
                                @enderror
                                
                                <div class="text-end">
                                    <button type="button" class="btn btn-danger btn-sm mt-2 remove-schedule">Eliminar</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                 <!-- Cupos -->
                <div class="mb-3">
                    <label for="available_spots" class="form-label">
                        Cupos por semana
                        @error('available_spots') <span class="text-danger">*{{ $message }}</span> @enderror
                    </label>
                    <input type="number" class="form-control @error('available_spots') is-invalid @enderror" 
                           id="available_spots" name="available_spots" value="{{ old('available_spots') }}" placeholder="Ej: 15" required>
                </div>

                <!-- Precios -->
                <div class="mb-3">
                    <h5>Precios por Sesiones Semanales</h5>
                    <div id="prices" class="mt-3">
                        @if(old('prices.weekly_sessions'))
                            @foreach (old('prices.weekly_sessions') as $index => $session)
                                <div class="border rounded p-3 mb-3">
                                    <label>
                                        Veces por Semana
                                        @error("prices.weekly_sessions.$index") <span class="text-danger">*{{ $message }}</span> @enderror
                                    </label>
                                    <input type="number" class="form-control" name="prices[weekly_sessions][]" value="{{ $session }}" required>
                                    
                                    <label>
                                        Precio
                                        @error("prices.price.$index") <span class="text-danger">*{{ $message }}</span> @enderror
                                    </label>
                                    <input type="number" class="form-control" name="prices[price][]" value="{{ old('prices.price')[$index] }}" required>
                                </div>
                            @endforeach
                        @else
                            <div class="border rounded p-3 mb-3">
                                <label>Veces por Semana</label>
                                <input type="number" class="form-control" name="prices[weekly_sessions][]" required>
                                <label>Precio</label>
                                <input type="number" class="form-control" name="prices[price][]" required>
                            </div>
                        @endif
                    </div>
                    <button type="button" id="add-price-button" class="btn-sm btn bg-naranja-light">Agregar Precio</button>
                </div>
                <!-- Fotos -->
                <div class="mb-3">
                    <label for="photos" class="form-label">
                        Fotos del Entrenamiento
                        @error('photos') <span class="text-danger">*{{ $message }}</span> @enderror
                    </label>
                    <input type="file" class="form-control" id="photos" name="photos[]" multiple accept="image/*">
                </div>
                <input type="hidden" id="photos_description" name="photos_description" value="Foto del entrenamiento.">

                <div class="text-end">
                    <a href="{{ route('trainer.calendar') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn text-white bg-naranja">Guardar Entrenamiento</button>
                </div>
            </form>
        </div>
    </div>
</main>
<script src="/js/entrenamientos/create.js"></script>
@endsection

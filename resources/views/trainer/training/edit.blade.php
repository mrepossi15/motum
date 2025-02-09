@extends('layouts.main')

@section('title', 'Editar Entrenamiento')

@section('content')

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<main class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-naranja text-white">
            <h2>Editar Entrenamiento</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('trainings.update', $training->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Título -->
                <div class="mb-3">
                    <label for="title" class="form-label">Título</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $training->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Descripción -->
                <div class="mb-3">
                    <label for="description" class="form-label">Descripción</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description', $training->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Nivel -->
                <div class="mb-3">
                    <label for="level" class="form-label">Nivel</label>
                    <select class="form-select @error('level') is-invalid @enderror" id="level" name="level" required>
                        <option value="Principiante" {{ old('level', $training->level) === 'Principiante' ? 'selected' : '' }}>Principiante</option>
                        <option value="Intermedio" {{ old('level', $training->level) === 'Intermedio' ? 'selected' : '' }}>Intermedio</option>
                        <option value="Avanzado" {{ old('level', $training->level) === 'Avanzado' ? 'selected' : '' }}>Avanzado</option>
                    </select>
                    @error('level')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Actividad -->
                <div class="mb-3">
                    <label for="activity_id" class="form-label">Actividad</label>
                    <select class="form-select @error('activity_id') is-invalid @enderror" id="activity_id" name="activity_id" required>
                        @foreach ($activities as $activity)
                            <option value="{{ $activity->id }}" {{ old('activity_id', $training->activity_id) == $activity->id ? 'selected' : '' }}>
                                {{ $activity->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('activity_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Parque -->
                <div class="mb-3">
                    <label for="park_id" class="form-label">Parque</label>
                    <select class="form-select @error('park_id') is-invalid @enderror" id="park_id" name="park_id" required>
                        @foreach ($parks as $park)
                            <option value="{{ $park->id }}" {{ old('park_id', $training->park_id) == $park->id ? 'selected' : '' }}>
                                {{ $park->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('park_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Días y Horarios -->
                @forelse ($filteredSchedules as $index => $schedule)
    <div class="border rounded p-3 mb-3">
        <label for="days-{{ $index }}">Días:</label>
        <div id="days-{{ $index }}">
            @foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $day)
                <label class="form-check-label">
                    <input type="checkbox" class="form-check-input @error("schedule.days.$index") is-invalid @enderror" 
                           name="schedule[days][{{ $index }}][]" 
                           value="{{ $day }}" 
                           {{ $schedule->day === $day ? 'checked' : '' }}> 
                    {{ $day }}
                </label>
            @endforeach
        </div>
        @error("schedule.days.$index")
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

        <label>Hora de Inicio:</label>
        <input type="time" class="form-control @error("schedule.start_time.$index") is-invalid @enderror" 
               name="schedule[start_time][{{ $index }}]" 
               value="{{ $schedule->start_time }}" required>
        @error("schedule.start_time.$index")
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

        <label>Hora de Fin:</label>
        <input type="time" class="form-control @error("schedule.end_time.$index") is-invalid @enderror" 
               name="schedule[end_time][{{ $index }}]" 
               value="{{ $schedule->end_time }}" required>
        @error("schedule.end_time.$index")
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

        <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeSchedule(this)">Eliminar</button>
    </div>
@empty
    <p>No hay horarios disponibles para editar.</p>
@endforelse
                <div class="mb-3">
                    <label for="available_spots" class="form-label">Cupos</label>
                    <input type="number" class="form-control @error('available_spots') is-invalid @enderror" id="available_spots" name="available_spots" value="{{ old('available_spots', $training->available_spots) }}" required>
                    @error('available_spots')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Precios -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Precios por Sesiones Semanales</h5>
                        <button type="button" id="add-price-button" class="btn-sm btn bg-naranja-light">Agregar Precio</button>
                    </div>
                    <div id="prices-container" class="mt-3">
                        @foreach ($training->prices as $index => $price)
                            <div class="border rounded p-3 mb-3">
                                <label>Veces por Semana:</label>
                                <input type="number" class="form-control @error("prices.weekly_sessions.$index") is-invalid @enderror" 
                                       name="prices[weekly_sessions][{{ $index }}]" 
                                       value="{{ $price->weekly_sessions }}" min="1" required>
                                @error("prices.weekly_sessions.$index")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <label>Precio:</label>
                                <input type="number" class="form-control @error("prices.price.$index") is-invalid @enderror" 
                                       name="prices[price][{{ $index }}]" 
                                       value="{{ $price->price }}" step="0.01" required>
                                @error("prices.price.$index")
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <button type="button" class="btn btn-danger mt-2" onclick="removePrice(this)">Eliminar</button>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="text-end">
                    <button type="submit" class="btn text-white bg-naranja">Guardar Cambios</button>
                    <a href="{{ route('trainer.training.show', $training->id) }}" class="btn btn-secondary">Cancelar</a>
                </div>
                <div class="mb-3">
                    <label for="photos" class="form-label">Fotos del Entrenamiento</label>
                    <input type="file" class="form-control" id="photos" name="photos[]" multiple accept="image/*">
                </div>
                <input type="hidden" id="photos_description" name="photos_description" value="Foto del entrenamiento.">

            </form>
        </div>
    </div>
</main>
<script src="/js/entrenamientos/edit.js"></script>

@endsection


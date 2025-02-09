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
        </div>
        <button class="btn btn-outline-danger favorite-btn" 
    data-id="{{ $training->id }}" 
    data-type="training">
    ❤️ Guardar
</button>
        <div class="card-body">
            <p><strong>Parque:</strong> {{ $training->park->name }}</p>
            <p><strong>Ubicación:</strong> {{ $training->park->location }}</p>
            <p><strong>Actividad:</strong> {{ $training->activity->name }}</p>
            <p><strong>Nivel:</strong> {{ $training->level }}</p>
            <p>
    <strong>Entrenador:</strong> 
    <a href="#" class="text-decoration-none text-naranja" data-bs-toggle="modal" data-bs-target="#trainerModal">
        {{ $training->trainer->name }}
    </a>
</p>
            <p><strong>Descripción:</strong> {{ $training->description ?? 'No especificada' }}</p>
            
            <h5>Horarios:</h5>
            <ul>
                @forelse ($training->schedules as $schedule)
                    <li>{{ ucfirst($schedule->day) }}: 
                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $schedule->start_time)->format('H:i') }} - 
                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $schedule->end_time)->format('H:i') }}
                    </li>
                @empty
                    <li>No hay horarios disponibles para este entrenamiento.</li>
                @endforelse
            </ul>

            <h5>Precios:</h5>
            <ul>
                @forelse ($training->prices as $price)
                    <li>{{ $price->weekly_sessions }} veces por semana: ${{ number_format($price->price, 2) }}</li>
                @empty
                    <li>No hay precios definidos para este entrenamiento.</li>
                @endforelse
            </ul>

            <h5>Participantes:</h5>
            <ul>
                <!-- Integrar con alumnos más adelante -->
                <li>0</li>
            </ul>
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
            <hr>
                
                <h5>Reseñas:</h5>

                @if($training->reviews->isEmpty())
                    <p>No hay reseñas para este entrenador todavía.</p>
                @else
                    @foreach($training->reviews as $review)
                        <div class="review mb-3">
                            <p><strong>Calificación:</strong> {{ $review->rating }} / 5</p>
                            <p><strong>Comentario:</strong> {{ $review->comment }}</p>
                            <p><small><strong>Autor:</strong> {{ $review->user->name }}</small></p>
                            <hr>
                        </div>
                    @endforeach
                @endif
                    <form action="{{ route('reviews.store') }}" method="POST">
            @csrf
            <input type="hidden" name="training_id" value="{{ $training->trainer->id }}">
            <div class="mb-3">
                <label for="rating" class="form-label">Calificación:</label>
                <select name="rating" id="rating" class="form-control" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="comment" class="form-label">Comentario:</label>
                <textarea name="comment" id="comment" class="form-control" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Enviar Reseña</button>
        </form>
        <div class="modal-footer">
    <form action="{{ route('cart.add') }}" method="POST">
        @csrf
        <input type="hidden" name="training_id" value="{{ $training->id }}">
        <div class="mb-3">
            <label for="weekly_sessions" class="form-label">Cantidad de veces por semana:</label>
            <select name="weekly_sessions" id="weekly_sessions" class="form-control" required>
                @foreach ($training->prices as $price)
                    <option value="{{ $price->weekly_sessions }}">
                        {{ $price->weekly_sessions }} veces por semana - ${{ $price->price }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn bg-naranja text-white">Comprar y reservar clase</button>
    </form>
</div>
        </div>
        <div class="card-footer text-end">
            @if (auth()->user()->role === 'entrenador' || auth()->user()->role === 'admin')
                <a href="{{ route('trainer.calendar') }}" class="btn btn-outline-secondary">Volver al calendario</a>
            @else
                <a href="{{ route('parks.show', $training->park->id) }}" class="btn btn-outline-secondary">Volver a clases</a>
            @endif
        </div>
    </div>
    
</div>
<div class="modal fade" id="trainerModal" tabindex="-1" aria-labelledby="trainerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-naranja text-white">
                <h5 class="modal-title" id="trainerModalLabel">Información del Entrenador</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Nombre:</strong> {{ $training->trainer->name }}</p>
                <p><strong>Email:</strong> {{ $training->trainer->email }}</p>

                @if($training->trainer->role === 'entrenador')
                    <p><strong>Certificación:</strong> {{ $training->trainer->certification ?? 'No especificada' }}</p>
                @endif

                <p><strong>Biografía:</strong></p>
                <p>{{ $training->trainer->biography ?? 'No especificada' }}</p>

                <hr>
                
                <h5>Reseñas:</h5>

                @if($training->trainer->reviews->isEmpty())
                    <p>No hay reseñas para este entrenador todavía.</p>
                @else
                    @foreach($training->trainer->reviews as $review)
                        <div class="review mb-3">
                            <p><strong>Calificación:</strong> {{ $review->rating }} / 5</p>
                            <p><strong>Comentario:</strong> {{ $review->comment }}</p>
                            <p><small><strong>Autor:</strong> {{ $review->user->name }}</small></p>
                            <hr>
                        </div>
                    @endforeach
                @endif
                <form action="{{ route('reviews.store') }}" method="POST">
            @csrf
            <input type="hidden" name="trainer_id" value="{{ $training->trainer->id }}">
            <div class="mb-3">
                <label for="rating" class="form-label">Calificación:</label>
                <select name="rating" id="rating" class="form-control" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="comment" class="form-label">Comentario:</label>
                <textarea name="comment" id="comment" class="form-control" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Enviar Reseña</button>
        </form>
            </div>
            
           
            <div class="modal-footer">
                <button type="button" class="btn bg-naranja text-white" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

</main>


@endsection

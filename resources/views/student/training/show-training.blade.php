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
            <button id="favorite-btn" 
                class="btn {{ $isFavorite ? 'btn-danger' : 'btn-outline-danger' }}" 
                data-id="{{ $training->id }}" 
                data-type="training"
                data-favorite="{{ $isFavorite ? 'true' : 'false' }}">
                ❤️ {{ $isFavorite ? 'Guardado' : 'Guardar' }}
            </button>
        </div>

        <div class="card-body">
            <p><strong>Parque:</strong> {{ $training->park->name }}</p>
            <p><strong>Ubicación:</strong> {{ $training->park->location }}</p>
            <p><strong>Actividad:</strong> {{ $training->activity->name }}</p>
            <p><strong>Nivel:</strong> {{ ucfirst($training->level) }}</p>
            <p><strong>Descripción:</strong> {{ $training->description ?? 'No especificada' }}</p>

            <p>
                <strong>Entrenador:</strong>
                <a href="#" class="text-decoration-none text-naranja" data-bs-toggle="modal" data-bs-target="#trainerModal">
                    {{ $training->trainer->name }}
                </a>
            </p>

            <h5>Horarios:</h5>
            <ul>
                @forelse ($training->schedules as $schedule)
                    <li>
                        {{ ucfirst($schedule->day) }}:
                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                    </li>
                @empty
                    <li>No hay horarios disponibles.</li>
                @endforelse
            </ul>

            <h5>Precios:</h5>
            <ul>
                @forelse ($training->prices as $price)
                    <li>{{ $price->weekly_sessions }} veces por semana: ${{ number_format($price->price, 2) }}</li>
                @empty
                    <li>No hay precios definidos.</li>
                @endforelse
            </ul>

            <h5>Fotos de Entrenamientos</h5>
            <div class="row">
                @forelse ($training->photos as $photo)
                    <div class="col-md-3 mb-3">
                        <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Entrenamiento" class="img-fluid rounded shadow-sm">
                    </div>
                @empty
                    <p class="text-muted">No hay fotos disponibles.</p>
                @endforelse
            </div>

            <hr>

            <h5>Reseñas</h5>

@if($training->reviews->isEmpty())
    <p>No hay reseñas para este entrenador.</p>
@else
    @foreach($training->reviews as $review)
        <div class="review mb-3 p-3 border rounded shadow-sm">
            <p><strong>Calificación:</strong> ⭐ {{ $review->rating }} / 5</p>
            <p><strong>Comentario:</strong> {{ $review->comment }}</p>
            <p><small><strong>Autor:</strong> {{ $review->user->name }}</small></p>

            <!-- Solo permitir eliminar si el usuario autenticado es el autor o un administrador -->
            @if(Auth::id() === $review->user_id || Auth::user()->role === 'admin')
                <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que quieres eliminar esta reseña?')">
                        ❌ Eliminar
                    </button>
                </form>
            @endif
            <hr>
        </div>
    @endforeach
@endif

            @auth
                @if($hasPurchased)
                    <form action="{{ route('reviews.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="training_id" value="{{ $training->id }}">
                        <div class="mb-3">
                            <label for="rating" class="form-label">Calificación:</label>
                            <select name="rating" id="rating" class="form-control" required>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="comment" class="form-label">Comentario:</label>
                            <textarea name="comment" id="comment" class="form-control" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Enviar Reseña</button>
                    </form>
                @else
                    <p class="text-muted">Debes haber comprado este entrenamiento para dejar una reseña.</p>
                @endif
            @endauth

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
            @if(auth()->user()->role === 'entrenador' || auth()->user()->role === 'admin')
                <a href="{{ route('trainer.calendar') }}" class="btn btn-outline-secondary">Volver al calendario</a>
            @else
                <a href="{{ route('parks.show', $training->park->id) }}" class="btn btn-outline-secondary">Volver a clases</a>
            @endif
        </div>
    </div>

    <!-- Modal Entrenador -->
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-naranja text-white" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    @if(session('cart_success'))
    <div class="modal fade" id="cartAddedModal" tabindex="-1" aria-labelledby="cartAddedLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cartAddedLabel">¡Agregado al carrito!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    ✅ {{ session('cart_success') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let cartModal = new bootstrap.Modal(document.getElementById("cartAddedModal"));
            cartModal.show();
        });
    </script>
@endif
    

</main>
<script>
document.addEventListener("DOMContentLoaded", function () {
    function attachFavoriteButtonListener() {
        let button = document.querySelector("#favorite-btn");

        if (!button) {
            console.warn("❌ No se encontró el botón de favoritos en el DOM. Reintentando...");
            setTimeout(attachFavoriteButtonListener, 500);
            return;
        }

        console.log("✅ Botón encontrado, adjuntando evento click.");

        // 🔥 Asegurar que el color inicial del botón coincida con la base de datos
        let isFavorite = button.dataset.favorite === "true";
        button.classList.toggle("btn-danger", isFavorite);
        button.classList.toggle("btn-outline-danger", !isFavorite);
        button.innerHTML = isFavorite ? "❤️ Guardado" : "❤️ Guardar";

        button.addEventListener("click", async function (event) {
            event.preventDefault();

            if (button.dataset.processing === "true") return;
            button.dataset.processing = "true";

            let favoritableId = button.dataset.id;
            let favoritableType = button.dataset.type;
            let isCurrentlyFavorite = button.classList.contains("btn-danger");

            // 🔥 Cambia el estado en la UI
            button.classList.toggle("btn-danger", !isCurrentlyFavorite);
            button.classList.toggle("btn-outline-danger", isCurrentlyFavorite);
            button.innerHTML = !isCurrentlyFavorite ? "❤️ Guardado" : "❤️ Guardar";

            try {
                let response = await fetch("/favorites/toggle", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({ favoritable_id: favoritableId, favoritable_type: favoritableType }),
                });

                if (!response.ok) {
                    throw new Error("Error en la respuesta del servidor");
                }

                let data = await response.json();
                console.log("✅ Respuesta del servidor:", data);

                if (data.status === "added") {
                    button.classList.add("btn-danger");
                    button.classList.remove("btn-outline-danger");
                    button.innerHTML = "❤️ Guardado";
                    button.dataset.favorite = "true"; // 🔥 Guardar estado en el dataset
                } else if (data.status === "removed") {
                    button.classList.remove("btn-danger");
                    button.classList.add("btn-outline-danger");
                    button.innerHTML = "❤️ Guardar";
                    button.dataset.favorite = "false"; // 🔥 Guardar estado en el dataset
                }

            } catch (error) {
                console.error("❌ Error en la solicitud:", error);

                // 🔥 Si hay error, revertir el cambio
                button.classList.toggle("btn-danger", isCurrentlyFavorite);
                button.classList.toggle("btn-outline-danger", !isCurrentlyFavorite);
                button.innerHTML = isCurrentlyFavorite ? "❤️ Guardado" : "❤️ Guardar";

                alert("Hubo un error al procesar la solicitud.");
            } finally {
                setTimeout(() => {
                    button.dataset.processing = "false";
                }, 1000);
            }
        });
    }

    attachFavoriteButtonListener();
});
</script>
@endsection
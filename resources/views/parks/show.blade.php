@extends('layouts.main')

@section('title', $park->name)

@section('content')
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="container mt-5 p-4 shadow-sm border rounded bg-light">
    <div class="bg-naranja text-white py-3 px-4 rounded-top">
        <h1 class="mb-0">{{ $park->name }}</h1>
    </div>
    @if($park->photo_urls)
    @php
        $photos = json_decode($park->photo_urls, true);
    @endphp
    @foreach($photos as $photo)
        <img src="{{ asset($photo) }}" alt="Foto de {{ $park->name }}" class="img-fluid rounded my-2">
    @endforeach
@endif
    <div class="p-4">
        <p class="mb-2"><strong>Ubicación:</strong> {{ $park->location }}</p>
        <p class="mb-2"><strong>Coordenadas:</strong> {{ $park->latitude }}, {{ $park->longitude }}</p>

        <h3 class="mt-4">Actividades Disponibles:</h3>
        <div class="row mt-3">
            @foreach($park->trainings->pluck('activity')->unique() as $activity)
                @if ($activity) <!-- Asegurarnos de que la actividad exista -->
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('activities.trainings', ['park' => $park->id, 'activity' => $activity->id]) }}" 
                           class="text-decoration-none">
                            <div class="activity-card p-3 text-center shadow-sm border rounded h-100">
                                <h5 class="text-naranja fw-bold">{{ $activity->name }}</h5>
                                <p class="text-muted">Explora entrenamientos relacionados</p>
                            </div>
                        </a>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    
    <a href="{{ route('map') }}"class="btn btn-secondary mt-3">Volver al mapa</a>
</div>
<button id="favorite-btn" 
    class="btn {{ $isFavorite ? 'btn-danger' : 'btn-outline-danger' }}" 
    data-id="{{ $park->id }}" 
    data-type="park"
    data-favorite="{{ $isFavorite ? 'true' : 'false' }}">
    ❤️ {{ $isFavorite ? 'Guardado' : 'Guardar' }}
</button>

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

@extends('layouts.main')

@section('title', 'Registro de Entrenador')

@section('content')

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="container mt-5 d-flex justify-content-center align-items-center">
    <div class="card shadow-sm p-4" style="width: 100%; max-width: 600px;">
        <h1 class="text-center mb-4 text-naranja">Agregar Parque</h1>
        <form action="{{ route('trainer.store.park') }}" method="POST" autocomplete="off">
            @csrf

            <div class="mb-3">
                <label for="park-search" class="form-label">Buscar un parque:</label>
                <input id="park-search" class="form-control" type="text" placeholder="Escribe el nombre del parque" required>
            </div>

            <!-- Mapa -->
            <div id="map" class="mb-3" style="height: 300px; border-radius: 8px; border: 1px solid #ccc;"></div>

            <!-- Campos ocultos -->
            <input type="hidden" id="park_name" name="park_name">
            <input type="hidden" id="lat" name="latitude">
            <input type="hidden" id="lng" name="longitude">
            <input type="hidden" id="location" name="location">
            <input type="hidden" id="opening_hours" name="opening_hours">
            <input type="hidden" id="photo_references" name="photo_references">

            <div class="d-grid">
                <button type="submit" class="btn bg-naranja text-white btn-block">Agregar</button>
            </div>
        </form>
    </div>
</div>

<!-- Scripts -->
<script src="/js/mapas/showMap.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.places_api_key') }}&libraries=places&callback=initAutocomplete" async defer></script>
@endsection

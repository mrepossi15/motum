@extends('layouts.main')

@section('title', 'Mapa de Parques')

@section('content')

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Contenedor de Búsqueda -->
<div class="container mt-4">
    <div class="row">
        <!-- Input de dirección con botón de búsqueda -->
        <div class="col-md-6">
            <div class="input-group">
                <input type="text" id="address-input" class="form-control" placeholder="Ingresa una dirección">
                
            </div>
        </div>
        <!-- Botón para recentrar la ubicación -->
        <div class="col-md-3">
            <button id="recenter-btn" class="btn btn-primary w-100">📍 Mi Ubicación</button>
        </div>
    </div>
</div>


<div class="container mt-3">
    <div class="row">
        <!-- Selección de Actividad -->
        <div class="col-md-4">
            <select id="activity-select" class="form-select">
                <option value="">Todas las actividades</option>
                @foreach($activities as $activity)
                    <option value="{{ $activity->id }}">{{ $activity->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Selección de Radio con botón de Aplicar -->
        <div class="col-md-4">
            <div class="input-group">
                <select id="radius-select" class="form-select">
                    <option value="1000">1 km</option>
                    <option value="2000">2 km</option>
                    <option value="3000">3 km</option>
                    <option value="4000">4 km</option>
                    <option value="5000" selected>5 km</option>
                    <option value="6000">6 km</option>
                    <option value="7000">7 km</option>
                    <option value="8000">8 km</option>
                    <option value="9000">9 km</option>
                    <option value="10000">10 km</option>
                </select>
                
            </div>
        </div>
    </div>
</div>

<!-- Mapa -->
<div id="map" class="mt-4" style="width: 100%; height: calc(100vh - 150px); position: relative;"></div>

<!-- Spinner de Carga -->
<div id="loading-spinner" class="d-flex justify-content-center align-items-center position-fixed top-0 start-0 w-100 h-100 bg-white d-none" style="z-index: 1051;">
    <div class="spinner-border text-naranja" role="status">
        <span class="visually-hidden">Cargando...</span>
    </div>
</div>

<script src="{{ asset('js/mapas/map.js') }}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.places_api_key') }}&libraries=places&callback=initMap" async defer></script>

@endsection
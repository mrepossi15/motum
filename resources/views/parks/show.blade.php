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
    <button class="btn btn-outline-danger favorite-btn" 
    data-id="{{ $park->id }}" 
    data-type="park">
    ❤️ Guardar
</button>
    <a href="{{ route('map') }}"class="btn btn-secondary mt-3">Volver al mapa</a>
</div>
@endsection

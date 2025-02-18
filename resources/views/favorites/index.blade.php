@extends('layouts.main')

@section('title', 'Mis Favoritos')

@section('content')
<div class="container mt-5">
    <h1>Mis Favoritos</h1>

    <h2>Entrenamientos Favoritos</h2>
    @if($favoriteTrainings->isEmpty())
        <p>No tienes entrenamientos guardados.</p>
    @else
        <ul class="list-group">
            @foreach($favoriteTrainings as $favorite)
                <li class="list-group-item">
                    <a href="{{ route('students-trainings.show', $favorite->favoritable_id) }}">
                        {{ $favorite->favoritable->title ?? 'Entrenamiento no disponible' }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

    <h2 class="mt-4">Parques Favoritos</h2>
    @if($favoriteParks->isEmpty())
        <p>No tienes parques guardados.</p>
    @else
        <ul class="list-group">
            @foreach($favoriteParks as $favorite)
                <li class="list-group-item">
                    <a href="{{ route('parks.show', $favorite->favoritable_id) }}">
                        {{ $favorite->favoritable->name ?? 'Parque no disponible' }}
                    </a>
                </li>
                
            @endforeach
        </ul>
    @endif
</div>
@endsection
@extends('layouts.main')

@section('title', "Detalle de la Clase")

@section('content')
<main class="container mt-4">
    <h2>{{ $training->title }} - {{ $date }}</h2>
    <p><strong>Ubicación:</strong> {{ $training->park->name }}</p>

    <h3>Reservas para esta Fecha</h3>
    <ul>
        @foreach($reservations as $reservation)
            <li>{{ $reservation->user->name }} - {{ $reservation->time }}</li>
        @endforeach
    </ul>

    <p><strong>Cupos Restantes:</strong> {{ $training->available_spots - $reservations->count() }}</p>
</main>
@endsection
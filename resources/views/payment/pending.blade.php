@extends('layouts.main')

@section('title', 'Pago Pendiente')

@section('content')
<div class="container mt-5 text-center">
    <h1 class="text-warning">Pago Pendiente</h1>
    <p>Tu pago está siendo procesado. Te notificaremos cuando se complete.</p>
    <a href="{{ url('/dashboard') }}" class="btn btn-primary">Volver al Dashboard</a>
</div>
@endsection
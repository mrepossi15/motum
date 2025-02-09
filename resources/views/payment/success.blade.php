@extends('layouts.main')

@section('title', 'Pago Exitoso')

@section('content')
<div class="container mt-5 text-center">
    <h1 class="text-success">¡Pago Exitoso!</h1>
    <p>Gracias por realizar tu compra. Ahora puedes disfrutar de tus entrenamientos.</p>
    <a href="{{ url('/dashboard') }}" class="btn btn-primary">Volver al Dashboard</a>
</div>
@endsection
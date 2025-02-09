@extends('layouts.main')

@section('title', 'Pago Fallido')

@section('content')
<div class="container mt-5 text-center">
    <h1 class="text-danger">El Pago Falló</h1>
    <p>Hubo un problema al procesar tu pago. Por favor, intenta nuevamente.</p>
    <a href="{{ url('/cart/view') }}" class="btn btn-warning">Volver al Carrito</a>
</div>
@endsection
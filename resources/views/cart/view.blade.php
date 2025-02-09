@extends('layouts.main')

@section('title', 'Carrito de Compras')

@section('content')
<div class="container mt-5">
    <h1>Carrito de Compras</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Título del Entrenamiento</th>
                <th>Entrenador</th>
                <th>Sesiones Semanales</th>
                <th>Precio</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($cartItems as $item)
                <tr>
                    <td>{{ $item->training->title }}</td>
                    <td>{{ $item->training->trainer->name }}</td>
                    <td>{{ $item->weekly_sessions }}</td>
                    <td>
                        ${{ $item->training->prices->where('weekly_sessions', $item->weekly_sessions)->first()->price }}
                    </td>
                    <td>
                    <form method="POST" action="{{ url('/cart/remove') }}">
                            @csrf
                            <input type="hidden" name="cart_item_id" value="{{ $item->id }}">
                            <button class="btn btn-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tu carrito está vacío.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @if ($cartItems->isNotEmpty())
    <form method="POST" action="{{ url('/cart/clear') }}">
        @csrf
        <button class="btn btn-warning mb-3">Vaciar Carrito</button>
    </form>
    @endif

    @if ($cartItems->isNotEmpty())
        <form method="POST" action="{{ url('/payment/split') }}">
            @csrf
            <button class="btn btn-primary">Proceder al Pago</button>
        </form>
    @endif
</div>
@endsection
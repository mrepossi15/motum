@extends('layouts.main')

@section('content')
<div class="container">
    <h2>Mis Compras</h2>
    
    @if($payments->isEmpty())
        <p>No has realizado ninguna compra aún.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Entrenamiento</th>
                    <th>Monto Total</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->training->title }}</td>
                        <td>${{ number_format($payment->total_amount, 2) }}</td>
                        <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($payment->status == 'approved')
                                <span class="badge bg-success">Aprobado</span>
                            @elseif($payment->status == 'pending')
                                <span class="badge bg-warning">Pendiente</span>
                            @else
                                <span class="badge bg-danger">Fallido</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compra Confirmada</title>
</head>
<body>
    <h2>¡Gracias por tu compra, {{ $user->name }}!</h2>
    <p>Has adquirido el entrenamiento <strong>{{ $training->title }}</strong> con éxito.</p>
    <p>Detalles de la compra:</p>
    <ul>
        <li><strong>Entrenador:</strong> {{ $training->trainer->name }}</li>
        <li><strong>Ubicación:</strong> {{ $training->park->name }} - {{ $training->park->location }}</li>
        <li><strong>Actividad:</strong> {{ $training->activity->name }}</li>
        <li><strong>Horario:</strong> {{ $training->schedules->first()->day }} a las {{ \Carbon\Carbon::parse($training->schedules->first()->start_time)->format('H:i') }}</li>
    </ul>
    <p>Si tienes alguna pregunta, puedes contactar al entrenador.</p>
    <p>¡Disfruta tu entrenamiento!</p>
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Compra</title>
</head>
<body>
    <h2>¡Felicidades, {{ $trainer->name }}!</h2>
    <p>{{ $user->name }} ha comprado tu entrenamiento <strong>{{ $training->title }}</strong>.</p>
    <p>Detalles de la compra:</p>
    <ul>
        <li><strong>Alumno:</strong> {{ $user->name }} - {{ $user->email }}</li>
        <li><strong>Ubicación:</strong> {{ $training->park->name }} - {{ $training->park->location }}</li>
        <li><strong>Actividad:</strong> {{ $training->activity->name }}</li>
        <li><strong>Horario:</strong> {{ $training->schedules->first()->day }} a las {{ \Carbon\Carbon::parse($training->schedules->first()->start_time)->format('H:i') }}</li>
    </ul>
    <p>¡Prepara un gran entrenamiento!</p>
</body>
</html>
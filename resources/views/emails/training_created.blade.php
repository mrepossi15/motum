<!DOCTYPE html>
<html>
<head>
    <title>Entrenamiento Creado</title>
</head>
<body>
    <h2>¡Hola, {{ $trainerName }}! 👋</h2>
    
    <p>Tu entrenamiento <strong>{{ $trainingTitle }}</strong> ha sido creado con éxito.</p>
    
    <p><strong>📍 Parque:</strong> {{ $parkName }}</p>
    <p><strong>🏋️ Actividad:</strong> {{ $activity }}</p>
    
    <h4>⏰ Horarios:</h4>
    <ul>
        @foreach ($schedule as $s)
            <li>{{ ucfirst($s->day) }}: {{ \Carbon\Carbon::createFromFormat('H:i:s', $s->start_time)->format('H:i') }} - {{ \Carbon\Carbon::createFromFormat('H:i:s', $s->end_time)->format('H:i') }}</li>
        @endforeach
    </ul>

    <p>¡Gracias por usar Motum! 🚀</p>
</body>
</html>
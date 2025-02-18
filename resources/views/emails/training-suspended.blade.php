<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clase Suspendida</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px;">

    <div style="max-width: 600px; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1);">
        <h2 style="color: #DA7744;">Motum - Clase Suspendida</h2>
        <p>Hola,</p>
        <p>Te informamos que la clase <strong>{{ $training->title }}</strong> programada para el día <strong>{{ \Carbon\Carbon::parse($date)->translatedFormat('l d/m/Y') }}</strong> ha sido suspendida.</p>
        <p><strong>Detalles:</strong></p>
        <ul>
            <li><strong>Actividad:</strong> {{ $training->activity->name }}</li>
            <li><strong>Parque:</strong> {{ $training->park->name }}</li>
            <li><strong>Entrenador:</strong> {{ $training->trainer->name }}</li>
        </ul>
        <p>Para más información, puedes contactar a tu entrenador.</p>
        <p>Saludos,<br>El equipo de Motum.</p>
    </div>

</body>
</html>
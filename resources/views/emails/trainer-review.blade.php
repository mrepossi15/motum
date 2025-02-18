<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Reseña Recibida</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px;">

    <div style="max-width: 600px; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1);">
        <h2 style="color: #DA7744;">Motum - ¡Has recibido una nueva reseña!</h2>
        <p>Hola <strong>{{ $trainer->name }}</strong>,</p>
        <p>Un alumno ha dejado una reseña sobre tu entrenamiento:</p>

        <p><strong>Calificación:</strong> ⭐ {{ $review->rating }} / 5</p>
        <p><strong>Comentario:</strong> "{{ $review->comment }}"</p>

        <p>¡Sigue haciendo un gran trabajo!</p>
        
        <p>Saludos,<br>El equipo de Motum.</p>
    </div>

</body>
</html>
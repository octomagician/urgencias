<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activación de Cuenta</title>
</head>
<body>
    <h1>Bienvenido al Grey Sloan's Memorial, {{ $user->name }}</h1>
    <p>{{ $contenido }}</p>
    <p>Por favor, activa tu cuenta haciendo clic en el siguiente enlace:</p>
    <a href="{{ $signedUrl }}">Activar Cuenta</a>
    <p>Este enlace expira en 5 minutos.</p>
</body>

</html>
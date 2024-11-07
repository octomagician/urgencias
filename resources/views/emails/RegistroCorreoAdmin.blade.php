<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autorización de Rol</title>
</head>
<body>
    <h2>Solicitud de Autorización de Rol para {{ $user->name }}</h2>
    
    <p>El usuario <strong>{{ $user->name }}</strong> ha activado su cuenta y ha solicitado el rol de <strong>{{ $user->requested_role }}</strong>.</p>
    
    <p>Para autorizar este rol, por favor haz clic en el siguiente enlace:</p>
    <a href="{{ $authorizationUrl }}">Autorizar Rol</a>
    
    <p>Este enlace expira en 5 minutos.</p>
    
    <p>Gracias,</p>
    <p>Equipo de Administración</p>
</body>
</html>

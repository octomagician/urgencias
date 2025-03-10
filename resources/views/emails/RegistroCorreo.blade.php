<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activación de Cuenta - Grey Sloan's Memorial</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #dddddd;
        }
        .header h1 {
            color: #333333;
            font-size: 24px;
            margin: 0;
        }
        .content {
            padding: 20px 0;
        }
        .content p {
            color: #555555;
            font-size: 16px;
            line-height: 1.6;
            margin: 0 0 20px;
        }
        .button-container {
            text-align: center;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            font-size: 16px;
            color: #ffffff;
            background-color: #007bff;
            border-radius: 5px;
            text-decoration: none;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #dddddd;
            color: #777777;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Encabezado -->
        <div class="header">
            <h1>Bienvenido al Grey Sloan's Memorial, {{ $user->name }}</h1>
        </div>

        <!-- Contenido -->
        <div class="content">
            <p>{{ $contenido }}</p>
            <p>Por favor, activa tu cuenta haciendo clic en el siguiente enlace:</p>
            <div class="button-container">
                <a href="{{ $signedUrl }}" class="button">Activar Cuenta</a>
            </div>
            <p>Este enlace expira en <strong>5 minutos</strong>.</p>
        </div>

        <!-- Pie de página -->
        <div class="footer">
            <p>Si no solicitaste este registro, por favor ignora este correo.</p>
            <p>&copy; {{ date('Y') }} Grey Sloan's Memorial. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
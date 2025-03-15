<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activación de Cuenta - Grey Sloan's Memorial</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #E0E1DD; /* Fondo claro */
            margin: 0;
            padding: 0;
            color: #0D1B2A; /* Texto oscuro */
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #FFFFFF; /* Fondo blanco */
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #1B263B; /* Azul oscuro */
            color: #E0E1DD; /* Texto claro */
            text-align: center;
            padding: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        .content {
            padding: 20px;
            color: #0D1B2A; /* Texto oscuro */
        }

        .content p {
            font-size: 16px;
            line-height: 1.6;
            margin: 0 0 20px;
        }

        .content a {
            color: #415A77; /* Azul intermedio */
            text-decoration: none;
            font-weight: bold;
        }

        .content a:hover {
            text-decoration: underline;
        }

        .button-container {
            text-align: center;
            margin: 20px 0;
        }

        .button-container strong {
            display: inline-block;
            padding: 15px 30px;
            background-color: #778DA9; /* Azul claro */
            color: #FFFFFF; /* Texto blanco */
            font-size: 20px;
            font-weight: bold;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .footer {
            background-color: #1B263B; /* Azul oscuro */
            color: #E0E1DD; /* Texto claro */
            text-align: center;
            padding: 15px;
            font-size: 14px;
        }

        .footer p {
            margin: 5px 0;
        }

        .footer a {
            color: #E0E1DD; /* Texto claro */
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
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
            <p>Por favor, activa tu cuenta ingresando el siguiente código en la página de verificación:</p>
            <p><a href="{{ env('FRONTEND_URI') }}/verificacion">Haz clic aquí para verificar tu cuenta</a></p>
            <div class="button-container">
                <strong>{{ $verificationCode }}</strong>
            </div>
            <p>Este código expira en <strong>5 minutos</strong>.</p>
        </div>

        <!-- Pie de página -->
        <div class="footer">
            <p>Si no solicitaste este registro, por favor ignora este correo.</p>
            <p>&copy; {{ date('Y') }} Grey Sloan's Memorial. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
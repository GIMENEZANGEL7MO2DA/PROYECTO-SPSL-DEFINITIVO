<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>INICIO - SPSL</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* General reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        body {
            background-color: #f3f0ff;
            color: #2c0a4a;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 10px;
        }

        .container {
            text-align: center;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            max-width: 100%; /* Ajuste a pantalla completa en móviles */
            width: 100%;
        }

        h1 {
            font-size: 2.2em;
            color: #4b007d;
            margin-bottom: 15px;
        }

        p {
            font-size: 1.2em;
            color: #5f2758;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        /* Ajustes para la imagen */
        img {
            width: 90%; /* Ocupa la mayoría del ancho del contenedor */
            max-width: 400px; /* Limita tamaño en pantallas grandes */
            height: auto;
            margin: 15px 0;
            border-radius: 8px;
        }

        /* Estilos de botón */
        .button {
            display: inline-block;
            margin: 10px 5px;
            padding: 15px 30px;
            color: #ffffff;
            background-color: #6a0dad;
            border: none;
            border-radius: 4px;
            font-size: 1.2em;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #4b007d;
        }

        /* Diseño responsive para pantallas pequeñas */
        @media (max-width: 768px) {
            h1 {
                font-size: 1.8em;
            }

            p {
                font-size: 1.1em;
            }

            img {
                width: 100%; /* Imagen completa en móviles */
                max-width: 300px; /* Tamaño máximo en móviles */
            }

            .button {
                font-size: 1.1em;
                padding: 12px 20px;
                width: 100%; /* Botones de ancho completo en móviles */
            }
        }

        /* Diseño responsive para pantallas grandes */
        @media (min-width: 1200px) {
            .container {
                max-width: 600px; /* Limitar ancho para evitar espacios grandes */
                padding: 30px;
            }

            h1 {
                font-size: 2.5em;
            }

            p {
                font-size: 1.2em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bienvenido a SPSL</h1>
        <p>SISTEMA DE PRESTACION DE SERVICIOS LABORALES</p>
        
        <!-- Imagen debajo del texto principal -->
        <img src="img/fotologo.jpeg" alt="Imagen descriptiva de SPSL">

        <p>La plataforma de ofertas de trabajo para trabajadores independientes.</p>
        <p>Conéctate con oportunidades laborales en tu área de profesion y especialización desde un entorno profesional y seguro, enfocado a los trabajadores que ejerzan de forma independiente.</p>

        <!-- Botones para iniciar sesión y registrarse -->
        <a href="login.php" class="button">Iniciar Sesión</a>
        <a href="register.php" class="button">Registrarse</a>
    </div>
</body>
</html>

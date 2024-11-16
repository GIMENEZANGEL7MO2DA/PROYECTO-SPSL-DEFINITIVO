<?php
session_start();
require 'includes/conexion.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $dni = $_POST['dni'];
    $nombre_usuario = $_POST['nombre_usuario'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nombre, apellido, correo, dni, nombre_usuario, password) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $nombre, $apellido, $correo, $dni, $nombre_usuario, $password);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;
        $insert_info_sql = "INSERT INTO informacion_usuarios (user_id, edad, profesion_id, especialidad_id, descripcion_personal, formacion_academica, formacion_laboral) 
                    VALUES (?, '', 1, NULL, '', '', '')";
        $insert_info_stmt = $conn->prepare($insert_info_sql);
        $insert_info_stmt->bind_param("i", $user_id);

        if ($insert_info_stmt->execute()) {
            $mensaje = "Registro exitoso. Ahora puedes <a href='login.php'>iniciar sesión</a>.";
        } else {
            $mensaje = "Error al insertar información adicional: " . $insert_info_stmt->error;
        }
    } else {
        $mensaje = "Error al registrar el usuario: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - SPSL</title>
    <style>
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
            padding: 10px;
            overflow-x: hidden;
        }

        .container {
            text-align: center;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }

        h2 {
            font-size: 2em;
            color: #4b007d;
            margin-bottom: 20px;
        }

        .message {
            margin-bottom: 20px;
            font-size: 1em;
            color: #28a745;
            background-color: #e0ffe0;
            padding: 10px;
            border-radius: 5px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-size: 1em;
            color: #5f2758;
            text-align: left;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
            width: 100%;
            box-sizing: border-box;
        }

        button {
            padding: 14px;
            background-color: #6a0dad;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            font-size: 1.2em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #4b007d;
        }

        .login-link {
            margin-top: 20px;
            font-size: 1em;
            color: #5f2758;
        }

        .login-link a {
            color: #6a0dad;
            font-weight: bold;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: #4b007d;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            h2 {
                font-size: 1.8em;
            }

            button {
                font-size: 1.1em;
                padding: 12px;
            }

            input[type="text"],
            input[type="email"],
            input[type="password"] {
                padding: 10px;
                font-size: 1em;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 5px;
            }

            .container {
                padding: 15px;
                width: 100%;
                box-sizing: border-box;
            }

            h2 {
                font-size: 1.6em;
            }

            button {
                font-size: 1em;
                padding: 12px;
            }

            input[type="text"],
            input[type="email"],
            input[type="password"] {
                padding: 10px;
                font-size: 1em;
            }

            .login-link {
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registro en SPSL</h2>
        
        <?php if ($mensaje): ?>
            <div class="message"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>
            
            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" required>
            
            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo" required>
            
            <label for="dni">DNI:</label>
            <input type="text" id="dni" name="dni" required>
            
            <label for="nombre_usuario">Nombre de Usuario:</label>
            <input type="text" id="nombre_usuario" name="nombre_usuario" required>
            
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Registrar</button>
        </form>

        <div class="login-link">
            <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
        </div>
    </div>
</body>
</html>

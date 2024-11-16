<?php
session_start();
require 'includes/conexion.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    $sql = "SELECT id, password FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            header("Location: paginaprincipal.php");
            exit();
        } else {
            $mensaje = "Contraseña incorrecta.";
        }
    } else {
        $mensaje = "Correo no encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - SPSL</title>
    <style>
        /* Reset general */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f3f0ff;
            color: #2c0a4a;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            text-align: center;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            font-size: 1.8em;
            color: #4b007d;
            margin-bottom: 20px;
        }

        .message {
            margin-bottom: 20px;
            font-size: 1em;
            color: #d9534f;
            background-color: #ffe0e0;
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

        input[type="email"],
        input[type="password"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
            width: 100%;
        }

        button {
            padding: 12px;
            background-color: #6a0dad;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #4b007d;
        }

        .register-link {
            margin-top: 15px;
        }

        .register-link a {
            color: #6a0dad;
            text-decoration: none;
            font-weight: bold;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        /* Responsive adjustments */
        @media (max-width: 600px) {
            h2 {
                font-size: 1.6em;
            }

            button {
                font-size: 1em;
                padding: 10px;
            }

            .container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Iniciar Sesión en SPSL</h2>
        
        <?php if ($mensaje): ?>
            <div class="message"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo" required>
            
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Iniciar sesión</button>
        </form>
        
        <div class="register-link">
            <p>¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a></p>
        </div>
    </div>
</body>
</html>

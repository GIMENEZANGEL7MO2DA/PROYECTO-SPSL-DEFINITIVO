<?php
session_start();
require 'includes/conexion.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener el ID del usuario que se muestra en el perfil
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';

if ($user_id) {
    // Consultar los datos del usuario incluyendo el nombre de la profesión y especialidad
    $sql = "SELECT u.id, u.nombre, u.apellido, u.correo, u.dni, u.nombre_usuario, 
                   i.edad, p.nombre AS profesion_nombre, e.nombre AS especialidad_nombre, 
                   i.descripcion_personal, i.formacion_academica, i.formacion_laboral
            FROM usuarios u
            LEFT JOIN informacion_usuarios i ON u.id = i.user_id
            LEFT JOIN profesiones p ON i.profesion_id = p.id
            LEFT JOIN especialidades e ON i.especialidad_id = e.id
            WHERE u.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "Usuario no encontrado.";
        exit();
    }
} else {
    echo "ID de usuario no especificado.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario - SPSL</title>
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
            flex-direction: column;
            align-items: center;
            padding: 0;
            margin: 0;
            min-height: 100vh;
        }

        h1 {
            font-size: 2.5em;
            color: #4b007d;
            margin: 30px 0;
            text-align: center;
            width: 100%;
            padding: 0 15px;
        }

        .profile-section {
            background-color: #9b4de5; /* Fondo morado claro dentro del recuadro */
            border: 2px solid #daa520; /* Borde dorado */
            border-radius: 8px;
            width: 90%;
            max-width: 800px;  /* Evitar que se haga muy grande */
            margin: 20px 0;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .profile-section h2 {
            color: #ffffff;
            font-size: 1.8em;
            margin-bottom: 15px;
            text-align: center;
        }

        .profile-section p {
            font-size: 1.2em;
            margin: 10px 0;
            color: #fff; /* Color blanco para texto */
        }

        .profile-section strong {
            color: #fff; /* Color blanco para texto destacado */
        }

        a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1em;
            margin-top: 30px;
            display: inline-block;
            padding: 10px 20px;
            border: 2px solid #daa520; /* Borde dorado */
            border-radius: 4px;
            transition: none; /* El color no cambia al pasar el ratón */
            background-color: #6a0dad; /* Color de fondo morado */
        }

        a:hover {
            background-color: #6a0dad; /* Fondo permanece igual */
        }

        /* Estilo Responsivo */
        @media (max-width: 600px) {
            body {
                padding: 20px;
            }

            h1 {
                font-size: 2.2em;
                padding: 0 15px;
            }

            .profile-section {
                width: 100%;
                margin: 10px 0;
                padding: 15px;
            }

            .profile-section p, .profile-section strong {
                font-size: 1.1em;
            }

            a {
                font-size: 1.1em;
                width: 100%;
                text-align: center;
                padding: 15px;
            }
        }
    </style>
</head>
<body>

    <h1>Perfil de: <?php echo htmlspecialchars($user['nombre_usuario']); ?></h1>

    <!-- Sección de Datos Básicos -->
    <div class="profile-section">
        <h2>Datos Básicos</h2>
        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($user['nombre']) . ' ' . htmlspecialchars($user['apellido']); ?></p>
        <p><strong>DNI:</strong> <?php echo htmlspecialchars($user['dni']); ?></p>
        <p><strong>Edad:</strong> <?php echo htmlspecialchars($user['edad']); ?></p>
    </div>

    <!-- Sección de Profesión y Especialidad -->
    <div class="profile-section">
        <h2>Profesión y Especialidad</h2>
        <p><strong>Profesión:</strong> <?php echo htmlspecialchars($user['profesion_nombre']); ?></p>
        <p><strong>Especialidad:</strong> <?php echo htmlspecialchars($user['especialidad_nombre']); ?></p>
    </div>

    <!-- Sección de Descripción Personal y Formación -->
    <div class="profile-section">
        <h2>Descripción y Formación</h2>
        <p><strong>Descripción Personal:</strong> <?php echo nl2br(htmlspecialchars($user['descripcion_personal'])); ?></p>
        <p><strong>Formación Académica:</strong> <?php echo nl2br(htmlspecialchars($user['formacion_academica'])); ?></p>
        <p><strong>Formación Laboral:</strong> <?php echo nl2br(htmlspecialchars($user['formacion_laboral'])); ?></p>
    </div>

    <a href="paginaprincipal.php">Volver a las ofertas</a>

</body>
</html>


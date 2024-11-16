<?php
session_start();
require 'includes/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener la información básica del usuario
$user_id = $_SESSION['user_id'];
$sql = "SELECT nombre, apellido, correo, dni, nombre_usuario FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

// Obtener información adicional del usuario
$info_sql = "SELECT edad, profesion_id, especialidad_id, descripcion_personal, formacion_academica, formacion_laboral FROM informacion_usuarios WHERE user_id = ?";
$info_stmt = $conn->prepare($info_sql);
$info_stmt->bind_param("i", $user_id);
$info_stmt->execute();
$info_result = $info_stmt->get_result();
$info_data = $info_result->fetch_assoc();

// Consultar profesiones
$profesiones_result = $conn->query("SELECT id, nombre FROM profesiones");

// Procesar actualización del correo y la información adicional del usuario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $edad = $_POST['edad'];
    $profesion_id = $_POST['profesion_id'];
    $especialidad_id = $_POST['especialidad_id'];
    $descripcion_personal = $_POST['descripcion_personal'];
    $formacion_academica = $_POST['formacion_academica'];
    $formacion_laboral = $_POST['formacion_laboral'];

    // Validar que la edad no sea negativa
    if ($edad < 0) {
        $message = "La edad no puede ser un número negativo.";
    } elseif (empty($profesion_id)) {
        // Validar que el campo de profesión no esté vacío
        $message = "Debe seleccionar una profesión antes de actualizar.";
    } elseif (empty($especialidad_id)) {
        // Validar que el campo de especialidad no esté vacío
        $message = "Debe seleccionar una especialidad antes de actualizar.";
    } else {
        // Actualizar el correo en la tabla usuarios
        $update_sql = "UPDATE usuarios SET correo = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("si", $correo, $user_id);
        $stmt->execute();

        // Actualizar o insertar información adicional
        if ($info_data) {
            $update_info_sql = "UPDATE informacion_usuarios SET edad = ?, profesion_id = ?, especialidad_id = ?, descripcion_personal = ?, formacion_academica = ?, formacion_laboral = ? WHERE user_id = ?";
            $stmt = $conn->prepare($update_info_sql);
            $stmt->bind_param("iiisssi", $edad, $profesion_id, $especialidad_id, $descripcion_personal, $formacion_academica, $formacion_laboral, $user_id);
        } else {
            $insert_info_sql = "INSERT INTO informacion_usuarios (user_id, edad, profesion_id, especialidad_id, descripcion_personal, formacion_academica, formacion_laboral) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_info_sql);
            $stmt->bind_param("iiisssi", $user_id, $edad, $profesion_id, $especialidad_id, $descripcion_personal, $formacion_academica, $formacion_laboral);
        }
        $stmt->execute();

        // Redirigir para evitar que se resubmitan los datos al recargar la página
        header("Location: mi_usuario.php");
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Usuario - SPSL</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e6e6f7; /* Fondo morado suave */
            margin: 0;
            padding: 0;
        }

        h1 {
            color: #4b0082; /* Morado oscuro */
            text-align: center;
        }

        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
            background-color: #4b0082; /* Morado oscuro */
            flex-wrap: wrap;
        }

        nav ul li {
            margin: 10px;
        }

        nav ul li a {
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            display: block;
            border: 2px solid #daa520; /* Borde dorado */
            border-radius: 5px;
            width: 160px; /* Tamaño uniforme para los botones */
            text-align: center;
            font-size: 16px; /* Tamaño más pequeño */
            box-sizing: border-box; /* Asegura que el padding no se sobreponga */
        }

        nav ul li a:hover {
            background-color: #5c2d91; /* Morado más claro */
        }

        /* Ajustes para pantallas pequeñas */
        @media (max-width: 768px) {
            nav ul {
                flex-direction: column; /* Los botones se apilan verticalmente */
                align-items: stretch; /* Alinea los botones para que ocupen todo el ancho */
            }

            nav ul li a {
                width: 100%; /* Hace que los botones ocupen todo el ancho disponible */
                font-size: 14px; /* Reduce el tamaño del texto */
                padding: 10px; /* Ajusta el padding para mejorar la legibilidad */
            }
        }

        form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 2px solid #daa520; /* Borde dorado */
            border-radius: 10px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #4b0082; /* Morado oscuro */
        }

        input[type="text"], input[type="email"], input[type="number"], textarea, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #daa520; /* Borde dorado */
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4b0082; /* Morado oscuro */
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #5c2d91; /* Morado más claro */
        }

        .message {
            text-align: center;
            color: red;
        }
    </style>
</head>
<body>

    <h1>Mi Usuario</h1>

    <nav>
        <ul>
            <li><a href="paginaprincipal.php">Inicio</a></li>
            <li><a href="agregar_oferta.php">Agregar Oferta</a></li>
            <li><a href="logout.php">Cerrar sesión</a></li>
        </ul>
    </nav>
    
    <form action="mi_usuario.php" method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($user_data['nombre']); ?>" disabled><br>

        <label for="apellido">Apellido:</label>
        <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($user_data['apellido']); ?>" disabled><br>

        <label for="correo">Correo:</label>
        <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($user_data['correo']); ?>" required><br>

        <label for="dni">DNI:</label>
        <input type="text" id="dni" name="dni" value="<?php echo htmlspecialchars($user_data['dni']); ?>" disabled><br>

        <label for="nombre_usuario">Nombre de Usuario:</label>
        <input type="text" id="nombre_usuario" name="nombre_usuario" value="<?php echo htmlspecialchars($user_data['nombre_usuario']); ?>" disabled><br>

        <label for="edad">Edad:</label>
        <input type="number" id="edad" name="edad" value="<?php echo htmlspecialchars($info_data['edad']); ?>" required><br>

        <label for="profesion_id">Profesión:</label>
        <select name="profesion_id" id="profesion_id" onchange="actualizarEspecialidades()" required>
            <option value="">Seleccionar Profesión</option>
            <?php
            while ($profesion = $profesiones_result->fetch_assoc()) {
                echo "<option value='" . $profesion['id'] . "' " . ($info_data['profesion_id'] == $profesion['id'] ? 'selected' : '') . ">" . htmlspecialchars($profesion['nombre']) . "</option>";
            }
            ?>
        </select><br>

        <label for="especialidad_id">Especialidad:</label>
        <select name="especialidad_id" id="especialidad_id" required>
            <option value="">Seleccionar Especialidad</option>
            <?php
            if (!empty($info_data['profesion_id'])) {
                $especialidades_result = $conn->query("SELECT id, nombre FROM especialidades WHERE profesion_id = " . $info_data['profesion_id']);
                while ($especialidad = $especialidades_result->fetch_assoc()) { ?>
                    <option value="<?php echo $especialidad['id']; ?>" <?php echo ($info_data['especialidad_id'] == $especialidad['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($especialidad['nombre']); ?>
                    </option>
                <?php }
            }
            ?>
        </select><br>

        <label for="descripcion_personal">Descripción Personal:</label>
        <textarea id="descripcion_personal" name="descripcion_personal"><?php echo htmlspecialchars($info_data['descripcion_personal']); ?></textarea><br>

        <label for="formacion_academica">Formación Académica:</label>
        <textarea id="formacion_academica" name="formacion_academica"><?php echo htmlspecialchars($info_data['formacion_academica']); ?></textarea><br>

        <label for="formacion_laboral">Formación Laboral:</label>
        <textarea id="formacion_laboral" name="formacion_laboral"><?php echo htmlspecialchars($info_data['formacion_laboral']); ?></textarea><br>

        <button type="submit">Actualizar</button>
    </form>

    <script>
        function actualizarEspecialidades() {
            const profesion_id = document.getElementById("profesion_id").value;
            const especialidadSelect = document.getElementById("especialidad_id");
            especialidadSelect.innerHTML = "<option value=''>Seleccionar Especialidad</option>";

            if (profesion_id) {
                fetch("get_especialidades.php?profesion_id=" + profesion_id)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(especialidad => {
                            const option = document.createElement("option");
                            option.value = especialidad.id;
                            option.text = especialidad.nombre;
                            especialidadSelect.add(option);
                        });
                    })
                    .catch(error => console.error("Error:", error));
            }
        }
    </script>

    <?php if (isset($message)) { ?>
        <div class="message"><?php echo $message; ?></div>
    <?php } ?>

</body>
</html>

<?php
session_start();
require 'includes/conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$sql_profesiones = "SELECT id, nombre FROM profesiones";
$stmt_profesiones = $conn->prepare($sql_profesiones);
$stmt_profesiones->execute();
$result_profesiones = $stmt_profesiones->get_result();

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $profesion_id = $_POST['profesion'];
    $especialidad_id = $_POST['especialidad'];
    $informacion = $_POST['informacion'];

    $user_id = $_SESSION['user_id'];
    $sql = "INSERT INTO ofertas_trabajo (user_id, profesion_id, especialidad_id, informacion)
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $user_id, $profesion_id, $especialidad_id, $informacion);

    if ($stmt->execute()) {
        $mensaje = "Oferta de trabajo agregada correctamente.";
    } else {
        $mensaje = "Error al agregar la oferta: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Oferta - SPSL</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        body {
            background-color: #f3f0ff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #2c0a4a;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
        }
        h1 {
            text-align: center;
            color: #4b007d;
            margin-bottom: 20px;
            font-size: 24px;
        }
        nav {
            margin-bottom: 20px;
        }
        nav ul {
            display: flex;
            gap: 20px;
            list-style: none;
            justify-content: center;
            flex-wrap: wrap;
        }
        nav a {
            color: #ffffff;
            background-color: #6a0dad;
            padding: 8px 16px;
            border: 2px solid #b8860b;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1em;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        nav a:hover {
            background-color: #4b007d;
            border-color: #daa520;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            width: 100%;
        }
        label {
            font-size: 14px;
            color: #333;
        }
        select, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #b8860b;
            background-color: #f3e5ff;
            border-radius: 4px;
            font-size: 1em;
            color: #4b007d;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        select:focus, textarea:focus {
            background-color: #e6ccff;
            border-color: #daa520;
            outline: none;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #6a0dad;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #4b007d;
        }
        .message {
            font-size: 1.1em;
            color: #4b007d;
            margin-top: 20px;
            text-align: center;
            background-color: #e6ccff;
            padding: 10px;
            border-radius: 5px;
        }
        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }
            h1 {
                font-size: 20px;
            }
            nav ul {
                flex-direction: column;
                align-items: center;
            }
            nav li {
                margin-bottom: 10px;
            }
            nav a, button {
                font-size: 1em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Agregar Nueva Oferta de Trabajo</h1>
        <nav>
            <ul>
                <li><a href="paginaprincipal.php">Inicio</a></li>
                <li><a href="agregar_oferta.php">Reiniciar Oferta</a></li>
                <li><a href="logout.php">Cerrar sesión</a></li>
            </ul>
        </nav>

        <?php if ($mensaje): ?>
            <div class="message"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>

        <form action="agregar_oferta.php" method="POST">
            <label for="profesion">Profesión:</label>
            <select name="profesion" id="profesion" required>
                <option value="">Selecciona una profesión</option>
                <?php while ($row_profesion = $result_profesiones->fetch_assoc()) { ?>
                    <option value="<?php echo $row_profesion['id']; ?>"><?php echo htmlspecialchars($row_profesion['nombre']); ?></option>
                <?php } ?>
            </select>

            <label for="especialidad">Especialidad:</label>
            <select name="especialidad" id="especialidad" required>
                <option value="">Selecciona una especialidad</option>
            </select>

            <label for="informacion">Información sobre la oferta:</label>
            <textarea name="informacion" id="informacion" rows="5" required></textarea>

            <button type="submit">Agregar Oferta</button>
        </form>
    </div>

    <script>
        document.getElementById('profesion').addEventListener('change', function() {
            var profesionId = this.value;
            var especialidadSelect = document.getElementById('especialidad');
            especialidadSelect.innerHTML = '<option value="">Selecciona una especialidad</option>';

            if (profesionId) {
                fetch('get_especialidades.php?profesion_id=' + profesionId)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(function(especialidad) {
                            var option = document.createElement('option');
                            option.value = especialidad.id;
                            option.textContent = especialidad.nombre;
                            especialidadSelect.appendChild(option);
                        });
                    });
            }
        });
    </script>
</body>
</html>

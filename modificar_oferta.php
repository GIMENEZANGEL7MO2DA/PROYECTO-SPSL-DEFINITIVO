<?php
session_start();
require 'includes/conexion.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verificar si se recibió el ID de la oferta
if (isset($_GET['id'])) {
    $oferta_id = $_GET['id'];

    // Consultar la oferta de trabajo para editarla
    $sql = "SELECT o.id, o.profesion_id, o.especialidad_id, o.informacion, p.nombre AS profesion, e.nombre AS especialidad
            FROM ofertas_trabajo o
            JOIN profesiones p ON o.profesion_id = p.id
            JOIN especialidades e ON o.especialidad_id = e.id
            WHERE o.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $oferta_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $oferta = $result->fetch_assoc();
    } else {
        echo "Oferta no encontrada.";
        exit();
    }
} else {
    echo "ID de la oferta no especificado.";
    exit();
}

// Procesar el formulario de actualización
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $profesion_id = $_POST['profesion'];
    $especialidad_id = $_POST['especialidad'];
    $informacion = $_POST['informacion'];

    // Actualizar la oferta de trabajo
    $sql_update = "UPDATE ofertas_trabajo 
                   SET profesion_id = ?, especialidad_id = ?, informacion = ? 
                   WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("iisi", $profesion_id, $especialidad_id, $informacion, $oferta_id);

    if ($stmt_update->execute()) {
        header("Location: paginaprincipal.php");
        exit();
    } else {
        echo "Error al actualizar la oferta.";
    }
}

// Obtener todas las profesiones
$profesiones_sql = "SELECT id, nombre FROM profesiones";
$profesiones_result = $conn->query($profesiones_sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modificar Oferta - SPSL</title>
    <style>
        /* Estilo general */
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f8; /* Color de fondo morado suave */
            color: #333;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #5c2d91; /* Morado suave */
            margin-top: 20px;
            font-size: 2rem;
        }

        /* Estilo para el formulario */
        form {
            background-color: #fff;
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 2px solid #d4af37; /* Recuadro dorado */
        }

        label {
            font-size: 1.1rem;
            color: #5c2d91; /* Morado suave */
            display: block;
            margin-bottom: 10px;
        }

        select, textarea, button {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            margin-bottom: 20px;
            border: 2px solid #d4af37; /* Borde dorado */
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        select:focus, textarea:focus, button:focus {
            outline: none;
            border-color: #9b59b6; /* Púrpura en foco */
        }

        button {
            background-color: #d4af37; /* Dorado */
            color: white;
            font-size: 1.2rem;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        button:hover {
            background-color: #f39c12; /* Dorado más oscuro */
        }

        button:active {
            background-color: #e67e22; /* Dorado más oscuro */
        }

        /* Estilos adicionales */
        select option {
            padding: 10px;
        }

        textarea {
            resize: vertical;
            min-height: 150px;
        }

        textarea:focus {
            border-color: #d4af37; /* Dorado en foco */
        }

        /* Responsividad */
        @media (max-width: 768px) {
            form {
                margin: 20px;
                padding: 15px;
            }

            h1 {
                font-size: 1.5rem;
            }
        }

    </style>
</head>
<body>
    <h1>Modificar Oferta de Trabajo</h1>

    <form action="modificar_oferta.php?id=<?php echo $oferta_id; ?>" method="POST">
        <label for="profesion">Profesión:</label>
        <select name="profesion" id="profesion" required onchange="cargarEspecialidades()">
            <option value="">Seleccione una profesión</option>
            <?php
            // Mostrar todas las profesiones
            while ($profesion = $profesiones_result->fetch_assoc()) {
                $selected = ($oferta['profesion_id'] == $profesion['id']) ? 'selected' : '';
                echo "<option value='".$profesion['id']."' $selected>".$profesion['nombre']."</option>";
            }
            ?>
        </select><br>

        <label for="especialidad">Especialidad:</label>
        <select name="especialidad" id="especialidad" required>
            <option value="">Seleccione una especialidad</option>
            <?php
            // Cargar especialidades dependiendo de la profesión seleccionada
            $especialidades_sql = "SELECT id, nombre FROM especialidades WHERE profesion_id = ?";
            $stmt_especialidades = $conn->prepare($especialidades_sql);
            $stmt_especialidades->bind_param("i", $oferta['profesion_id']);
            $stmt_especialidades->execute();
            $especialidades_result = $stmt_especialidades->get_result();

            while ($especialidad = $especialidades_result->fetch_assoc()) {
                $selected = ($oferta['especialidad_id'] == $especialidad['id']) ? 'selected' : '';
                echo "<option value='".$especialidad['id']."' $selected>".$especialidad['nombre']."</option>";
            }
            ?>
        </select><br>

        <label for="informacion">Información sobre la oferta:</label>
        <textarea name="informacion" id="informacion" required><?php echo htmlspecialchars($oferta['informacion']); ?></textarea><br>

        <button type="submit">Actualizar Oferta</button>
    </form>

    <script>
        // Función para cargar especialidades de acuerdo con la profesión seleccionada
        function cargarEspecialidades() {
            var profesionId = document.getElementById('profesion').value;
            var especialidadSelect = document.getElementById('especialidad');

            // Vaciar el select de especialidades
            especialidadSelect.innerHTML = "<option value=''>Seleccione una especialidad</option>";

            if (profesionId != "") {
                // Hacer una petición AJAX para obtener las especialidades de la profesión seleccionada
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "get_especialidades.php?profesion_id=" + profesionId, true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        var especialidades = JSON.parse(xhr.responseText);
                        especialidades.forEach(function(especialidad) {
                            var option = document.createElement("option");
                            option.value = especialidad.id;
                            option.textContent = especialidad.nombre;
                            especialidadSelect.appendChild(option);
                        });
                    }
                };
                xhr.send();
            }
        }
    </script>

</body>
</html>

<?php
session_start();
require 'includes/conexion.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Inicializar las variables de filtro
$profesion_filter = isset($_GET['profesion']) ? $_GET['profesion'] : '';
$especialidad_filter = isset($_GET['especialidad']) ? $_GET['especialidad'] : '';
$usuario_filter = isset($_GET['usuario']) ? $_GET['usuario'] : '';

// Consultar todas las profesiones
$profesiones_sql = "SELECT id, nombre FROM profesiones";
$profesiones_result = $conn->query($profesiones_sql);

// Consultar todas las especialidades
$especialidades_sql = "SELECT id, nombre FROM especialidades";
$especialidades_result = $conn->query($especialidades_sql);

// Consultar las ofertas de trabajo basadas en los filtros
$sql = "SELECT o.id, u.id AS user_id, u.nombre_usuario, p.nombre AS profesion, e.nombre AS especialidad, o.informacion
        FROM ofertas_trabajo o
        JOIN usuarios u ON o.user_id = u.id
        JOIN profesiones p ON o.profesion_id = p.id
        JOIN especialidades e ON o.especialidad_id = e.id
        WHERE 1";

// Agregar filtros si están presentes
if ($profesion_filter) {
    $sql .= " AND p.id = ?";
}
if ($especialidad_filter) {
    $sql .= " AND e.id = ?";
}
if ($usuario_filter) {
    $sql .= " AND u.nombre_usuario LIKE ?";
}

$sql .= " ORDER BY o.fecha_publicacion DESC";

$stmt = $conn->prepare($sql);
if ($profesion_filter && $especialidad_filter && $usuario_filter) {
    $usuario_filter = "%$usuario_filter%"; // Usar LIKE para búsqueda parcial
    $stmt->bind_param("ii", $profesion_filter, $especialidad_filter, $usuario_filter);
} elseif ($profesion_filter && $especialidad_filter) {
    $stmt->bind_param("ii", $profesion_filter, $especialidad_filter);
} elseif ($profesion_filter && $usuario_filter) {
    $usuario_filter = "%$usuario_filter%";
    $stmt->bind_param("is", $profesion_filter, $usuario_filter);
} elseif ($especialidad_filter && $usuario_filter) {
    $usuario_filter = "%$usuario_filter%";
    $stmt->bind_param("is", $especialidad_filter, $usuario_filter);
} elseif ($profesion_filter) {
    $stmt->bind_param("i", $profesion_filter);
} elseif ($especialidad_filter) {
    $stmt->bind_param("i", $especialidad_filter);
} elseif ($usuario_filter) {
    $usuario_filter = "%$usuario_filter%";
    $stmt->bind_param("s", $usuario_filter);
}

$stmt->execute();
$result = $stmt->get_result();

// Obtener especialidades por profesión
$especialidades_sql = "SELECT id, nombre, profesion_id FROM especialidades";
$especialidades_result = $conn->query($especialidades_sql);

// Organizar las especialidades por profesión para facilitar la búsqueda
$especialidades_por_profesion = [];
while ($especialidad = $especialidades_result->fetch_assoc()) {
    $especialidades_por_profesion[$especialidad['profesion_id']][] = $especialidad;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pagina Principal SPSL</title>
    <link rel="stylesheet" href="styles2.css">
    <script>
        // Función para actualizar las especialidades según la profesión seleccionada
        function actualizarEspecialidades() {
            var profesionId = document.getElementById('profesion').value;
            var especialidadSelect = document.getElementById('especialidad');

            // Limpiar opciones anteriores
            especialidadSelect.innerHTML = '<option value="">Seleccionar Especialidad</option>';

            // Si se selecciona una profesión
            if (profesionId) {
                // Obtener las especialidades correspondientes a la profesión seleccionada
                var especialidades = <?php echo json_encode($especialidades_por_profesion); ?>;
                if (especialidades[profesionId]) {
                    // Mostrar las especialidades
                    especialidades[profesionId].forEach(function (especialidad) {
                        var option = document.createElement('option');
                        option.value = especialidad.id;
                        option.textContent = especialidad.nombre;
                        especialidadSelect.appendChild(option);
                    });
                }
            }
        }
    </script>
</head>
<body>
    <h1>Bienvenido a SPSL</h1>
    <div class="logo-container">
    <img src="img/fotologo.jpeg" alt="Logo" class="logo-img">
</div>

    <style>
.logo-container {
        display: flex;
        justify-content: center; /* Centrado horizontal */
        margin: 0 auto; /* Centrado general */
        padding: 20px 0; /* Espacio superior e inferior */
    }

    /* Imagen más grande y centrada */
    .logo-img {
        max-width: 60%;
        height: auto;
        aspect-ratio: 3 / 1;
        border-radius: 10px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        object-fit: cover;
    }

    /* Ajustes específicos para pantallas pequeñas */
    @media (max-width: 768px) {
        .logo-img {
            max-width: 80%;
        }
    }

    @media (max-width: 600px) {
        .logo-img {
            max-width: 90%;
        }
    }
       * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    background-color: #f3f0ff; /* Morado suave */
    color: #2c0a4a;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    min-height: 100vh;
    justify-content: flex-start;
}

h1 {
    font-size: 2.5em;
    color: #4b007d;
    margin-bottom: 20px;
    text-align: center;
}

nav {
    margin-bottom: 30px;
    width: 100%;
}

nav ul {
    display: flex;
    gap: 20px;
    list-style: none;
    justify-content: center;
}

nav a {
    color: #6a0dad;
    text-decoration: none;
    font-weight: bold;
    font-size: 1.1em;
}

nav a:hover {
    color: #4b007d;
}

form {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
    margin-bottom: 30px;
    width: 100%;
    max-width: 600px;
    background-color: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    border: 2px solid #ffd700; /* Recuadros dorados */
}

form select, form input[type="text"] {
    padding: 10px;
    font-size: 1em;
    border-radius: 4px;
    width: 100%;
    border: 1px solid #ccc;
}

form button {
    padding: 12px;
    background-color: #6a0dad;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1.1em;
    transition: background-color 0.3s;
}

form button:hover {
    background-color: #4b007d;
}

.oferta {
    background-color: #ffffff;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 10px;
    box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 800px;
    border: 2px solid #ffd700; /* Recuadros dorados */
}

.oferta h2 {
    font-size: 1.8em;
    color: #6a0dad;
    margin-bottom: 10px;
}

.oferta p {
    font-size: 1.1em;
    color: #5f2758;
    margin-bottom: 10px;
}

.oferta p strong {
    color: #6a0dad;
}

.oferta a {
    font-size: 1.1em;
    color: #6a0dad;
    text-decoration: none;
    margin-right: 15px;
    border: 2px solid #6a0dad;
    padding: 8px 20px;
    border-radius: 5px;
    transition: background-color 0.3s, color 0.3s;
}

.oferta a:hover {
    color: white;
    background-color: #4b007d;
    border-color: #4b007d;
}

.oferta form {
    display: inline;
}

.oferta form button {
    padding: 8px 20px;
    background-color: #ffca28;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1.1em;
    margin-top: 15px;
    transition: background-color 0.3s;
}

.oferta form button:hover {
    background-color: #ff9800;
}

@media (max-width: 768px) {
    body {
        padding: 20px 10px;
    }

    h1 {
        font-size: 2.2em;
    }

    nav ul {
        flex-direction: column;
        align-items: center;
    }

    form button {
        font-size: 1em;
    }

    .oferta h2 {
        font-size: 1.4em;
    }

    .oferta p {
        font-size: 1em;
    }

    .oferta a, .oferta form button {
        font-size: 1em;
        padding: 8px 15px;
    }
}

@media (max-width: 600px) {
    h1 {
        font-size: 1.8em;
    }

    form button {
        font-size: 1em;
    }

    .oferta h2 {
        font-size: 1.3em;
    }

    .oferta p {
        font-size: 0.9em;
    }

    .oferta a, .oferta form button {
        font-size: 1em;
        padding: 6px 10px;
    }
}
    </style>
    <nav>
    <ul>
        <li><a href="paginaprincipal.php">Inicio</a></li>
        <li><a href="agregar_oferta.php">Agregar Oferta</a></li>
        <li><a href="mi_usuario.php">Mi Usuario</a></li> <!-- Botón agregado para ir a perfil.php -->
        <li><a href="logout.php">Cerrar sesión</a></li>
    </ul>
</nav>

    <!-- Formulario de filtrado -->
    <form action="paginaprincipal.php" method="GET">
        <label for="profesion">Filtrar por Profesión:</label>
        <select name="profesion" id="profesion" onchange="actualizarEspecialidades()">
            <option value="">Seleccionar Profesión</option>
            <?php while ($profesion = $profesiones_result->fetch_assoc()) { ?>
                <option value="<?php echo $profesion['id']; ?>" <?php echo ($profesion_filter == $profesion['id']) ? 'selected' : ''; ?>>
                    <?php echo $profesion['nombre']; ?>
                </option>
            <?php } ?>
        </select>

        <label for="especialidad">Filtrar por Especialidad:</label>
        <select name="especialidad" id="especialidad">
            <option value="">Seleccionar Especialidad</option>
            <?php
            // Si ya se ha filtrado por profesión, mostrar las especialidades correspondientes
            if ($profesion_filter && isset($especialidades_por_profesion[$profesion_filter])) {
                foreach ($especialidades_por_profesion[$profesion_filter] as $especialidad) {
                    echo "<option value='".$especialidad['id']."' ".($especialidad_filter == $especialidad['id'] ? 'selected' : '').">".$especialidad['nombre']."</option>";
                }
            }
            ?>
        </select>

        <label for="usuario">Buscar por Nombre de Usuario:</label>
        <input type="text" name="usuario" id="usuario" value="<?php echo htmlspecialchars($usuario_filter ? str_replace('%', '', $usuario_filter) : ''); ?>" placeholder="Nombre de usuario">


        <button type="submit">Filtrar</button>
    </form>
<?php
    // Ejecutar la consulta
$stmt->execute();
$result = $stmt->get_result();

// Verificar si la consulta devolvió resultados
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        ?>
        <div class="oferta">
            <h2>Oferta de: <?php echo htmlspecialchars($row['nombre_usuario']); ?></h2>
            <p><strong>Profesión:</strong> <?php echo htmlspecialchars($row['profesion']); ?></p>
            <p><strong>Especialidad:</strong> <?php echo htmlspecialchars($row['especialidad']); ?></p>
            <p><strong>Información sobre la oferta:</strong> <?php echo nl2br(htmlspecialchars($row['informacion'])); ?></p>

            <!-- Agregar el botón para ver el perfil -->
            <a href="perfil.php?user_id=<?php echo $row['user_id']; ?>">Ver Perfil</a>

            <!-- Verificar si la oferta fue publicada por el usuario logueado -->
            <?php if ($row['user_id'] == $_SESSION['user_id']) { ?>
                <!-- Botón para modificar la oferta -->
                <a href="modificar_oferta.php?id=<?php echo $row['id']; ?>">Modificar Oferta</a>
                
                <!-- Botón para eliminar la oferta -->
                <form action="eliminar_oferta.php" method="POST" style="display:inline;">
                    <input type="hidden" name="oferta_id" value="<?php echo $row['id']; ?>">
                    <button type="submit">Eliminar Oferta</button>
                </form>
            <?php } ?>
        </div>
        <?php
    }
} else {
    echo "No se encontraron ofertas.";
}     
?>

</body>
</html>
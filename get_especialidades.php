<?php
require 'includes/conexion.php';

if (isset($_GET['profesion_id'])) {
    $profesion_id = $_GET['profesion_id'];

    // Obtener las especialidades de la profesión seleccionada
    $sql = "SELECT id, nombre FROM especialidades WHERE profesion_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $profesion_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $especialidades = [];
    while ($row = $result->fetch_assoc()) {
        $especialidades[] = $row;
    }

    echo json_encode($especialidades);
}
?>

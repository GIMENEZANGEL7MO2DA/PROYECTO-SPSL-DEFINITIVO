<?php
session_start();
require 'includes/conexion.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verificar si se recibió el ID de la oferta
if (isset($_POST['oferta_id'])) {
    $oferta_id = $_POST['oferta_id'];
    
    // Consultar si el usuario que publica la oferta es el mismo que está logueado
    $sql = "SELECT user_id FROM ofertas_trabajo WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $oferta_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Si la oferta existe
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Verificar que el usuario logueado es el mismo que publicó la oferta
        if ($row['user_id'] == $_SESSION['user_id']) {
            // Eliminar la oferta
            $sql_delete = "DELETE FROM ofertas_trabajo WHERE id = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("i", $oferta_id);

            if ($stmt_delete->execute()) {
                // Redirigir al usuario a la página principal después de eliminar la oferta
                header("Location: paginaprincipal.php");
                exit();
            } else {
                echo "Error al eliminar la oferta: " . $stmt_delete->error;
            }
        } else {
            echo "No tienes permiso para eliminar esta oferta.";
        }
    } else {
        echo "La oferta no existe.";
    }
}
?>

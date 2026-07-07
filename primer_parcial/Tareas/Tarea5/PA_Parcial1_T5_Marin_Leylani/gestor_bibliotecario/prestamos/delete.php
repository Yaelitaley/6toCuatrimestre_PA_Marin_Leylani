<?php
    require_once "../config/connection.php";

    $id = intval($_GET["id"] ?? 0);

    if ($id <= 0) {
        header("Location: index.php?error=" . urlencode("ID inválido"));
        exit;
    }

    // La tabla detalle_prestamo tiene ON DELETE CASCADE hacia prestamos,
    // lo que significa que al eliminar un préstamo, sus detalles se borran automáticamente.
    // Aun así usamos prepared statement para el DELETE principal.
    $stmt = $conn->prepare("DELETE FROM prestamos WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: index.php?success=" . urlencode("Préstamo eliminado exitosamente"));
        exit;
    } else {
        header("Location: index.php?error=" . urlencode("Error al eliminar el préstamo: " . $stmt->error));
        exit;
    }
?>

<?php
    require_once "../config/connection.php";

    $id = intval($_GET["id"] ?? 0);

    if ($id <= 0) {
        header("Location: index.php?error=" . urlencode("ID inválido"));
        exit;
    }

    // Aquí va el código para eliminar el autor usando una consulta preparada
    $stmt = $conn->prepare("DELETE FROM autores WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: index.php?success=" . urlencode("Autor eliminado con éxito."));
        exit;
    } else {
        header("Location: index.php?error=" . urlencode("Error al eliminar el autor: " . $stmt->error));
        exit;
    }
?>

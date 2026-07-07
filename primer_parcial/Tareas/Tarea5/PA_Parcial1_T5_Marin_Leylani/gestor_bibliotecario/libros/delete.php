<?php
    require_once "../config/connection.php";

    $id = intval($_GET["id"] ?? 0);

    if ($id <= 0) {
        header("Location: index.php?error=" . urlencode("ID inválido"));
        exit;
    }

    // Aquí va el código para eliminar el libro usando una consulta preparada
     $stmt = $conn->prepare("DELETE FROM libros WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: index.php?success=" . urlencode("Libro eliminado con éxito."));
        exit;
    } else {
        header("Location: index.php?error=" . urlencode("Error al eliminar este Libro: " . $stmt->error));
        exit;
    }
?>

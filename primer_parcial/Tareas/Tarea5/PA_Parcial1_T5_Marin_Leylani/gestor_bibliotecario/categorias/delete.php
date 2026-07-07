<?php
    require_once "../config/connection.php";

    $id = intval($_GET["id"] ?? 0);

    if ($id <= 0) {
        header("Location: index.php?error=" . urlencode("ID inválido"));
        exit;
    }

    // Aquí va el código para eliminar la categoría usando una consulta preparada
    $stmt = $conn->prepare("DELETE FROM categorias WHERE id = ?"); // Consulta preparada para eliminar la categroría, tiene un marcador de posición para el ID.
    $stmt->bind_param("i", $id); // Vincula el ID a la consulta preparada, indicando que es un entero ("i").
    if($stmt->execute()){
        header("Location: index.php?success=" . urlencode("Categoría eliminada con éxito."));
        exit;
    }else{
        header("Location: index.php?error" . urlencode("Error al eliminar la categoría: " . $stmt->error));
        exit;
    }
?>

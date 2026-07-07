<?php
    require_once "../config/connection.php";

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Location: index.php");
        exit;
    }

    $id               = intval($_POST["id"] ?? 0);
    $titulo           = trim($_POST["titulo"] ?? "");
    $isbn             = trim($_POST["isbn"] ?? "");
    $anio_publicacion = trim($_POST["anio_publicacion"] ?? "") ?: null;
    $editorial        = trim($_POST["editorial"] ?? "");
    $cantidad         = intval($_POST["cantidad"] ?? 0);
    $disponibles      = intval($_POST["disponibles"] ?? 0);
    $id_categoria     = intval($_POST["id_categoria"] ?? 0);
    $id_autores       = $_POST["id_autores"] ?? [];

    if ($id <= 0 || $titulo === "" || $id_categoria <= 0 || empty($id_autores)) {
        header("Location: index.php?error=" . urlencode("Datos inválidos"));
        exit;
    }

    // Aquí va el código para actualizar el libro usando una consulta preparada
    $stmt = $conn->prepare("UPDATE libros SET titulo = ?, isbn = ?, anio_publicacion = ?, editorial = ?, cantidad = ?, disponibles = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $titulo, $isbn, $anio_publicacion, $editorial, $cantidad, $disponibles, $id);

    if ($stmt->execute()) {
        header("Location: index.php?success=" . urlencode("Libro actualizado exitosamente"));
        exit;
    } else {
        header("Location: index.php?error=" . urlencode("Error al actualizar este Libro: " . $stmt->error));
        exit;
    }
    $stmt->close();
    $conn->close();

    // Aquí va el código para eliminar los autores actuales del libro usando una consulta preparada
    // Aquí va el código para insertar los nuevos autores en libro_autor usando una consulta preparada

?>

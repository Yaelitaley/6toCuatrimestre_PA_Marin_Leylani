<?php
    require_once "../config/connection.php";

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Location: index.php");
        exit;
    }

    // Recibimos y sanitizamos los campos
    $id              = intval($_POST["id"]              ?? 0);
    $id_usuario      = intval($_POST["id_usuario"]      ?? 0);
    $fecha_prestamo  = trim($_POST["fecha_prestamo"]    ?? "");
    $fecha_devolucion = trim($_POST["fecha_devolucion"] ?? "");
    $estado          = trim($_POST["estado"]            ?? "");

    // Validamos que todos los campos tengan valor válido
    if ($id <= 0 || $id_usuario <= 0 || $fecha_prestamo === "" || $fecha_devolucion === "" || $estado === "") {
        header("Location: index.php?error=" . urlencode("Datos inválidos"));
        exit;
    }

    if (strtotime($fecha_devolucion) <= strtotime($fecha_prestamo)) {
        header("Location: edit.php?id=$id&error=" . urlencode("La fecha de devolución debe ser posterior a la fecha de préstamo"));
        exit;
    }

    $estados_validos = ["prestado", "devuelto", "retrasado"];
    if (!in_array($estado, $estados_validos)) {
        header("Location: edit.php?id=$id&error=" . urlencode("Estado no válido"));
        exit;
    }

    // Actualizamos el préstamo con prepared statement
    $stmt = $conn->prepare("UPDATE prestamos SET id_usuario = ?, fecha_prestamo = ?, fecha_devolucion = ?, estado = ? WHERE id = ?");
    // "isssi": i=id_usuario, s=fecha_prestamo, s=fecha_devolucion, s=estado, i=id
    $stmt->bind_param("isssi", $id_usuario, $fecha_prestamo, $fecha_devolucion, $estado, $id);

    if ($stmt->execute()) {
        header("Location: index.php?success=" . urlencode("Préstamo actualizado exitosamente"));
        exit;
    } else {
        header("Location: index.php?error=" . urlencode("Error al actualizar el préstamo: " . $stmt->error));
        exit;
    }

    $stmt->close();
    $conn->close();
?>

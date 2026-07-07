<?php
    require_once "../config/connection.php";

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Location: index.php");
        exit;
    }

    $id               = intval($_POST["id"] ?? 0);
    $nombre           = trim($_POST["nombre"] ?? "");
    $apellido         = trim($_POST["apellido"] ?? "");
    $nacionalidad     = trim($_POST["nacionalidad"] ?? "");
    $fecha_nacimiento = trim($_POST["fecha_nacimiento"] ?? "") ?: null;

    if ($id <= 0 || $nombre === "" || $apellido === "") {
        header("Location: index.php?error=" . urlencode("Datos inválidos"));
        exit;
    }

    // Aquí va el código para actualizar el autor usando una consulta preparada
    $stmt = $conn->prepare("UPDATE autores SET nombre = ?, apellido = ?, nacionalidad = ?, fecha_nacimiento = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $nombre, $apellido, $nacionalidad, $fecha_nacimiento, $id);

    if ($stmt->execute()) {
        header("Location: index.php?success=" . urlencode("Autor actualizado exitosamente"));
        exit;
    } else {
        header("Location: index.php?error=" . urlencode("Error al actualizar el autor: " . $stmt->error));
        exit;
    }
    $stmt->close();
    $conn->close();
?>

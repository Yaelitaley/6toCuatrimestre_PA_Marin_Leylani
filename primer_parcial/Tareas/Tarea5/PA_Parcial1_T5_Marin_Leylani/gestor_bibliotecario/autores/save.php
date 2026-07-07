<?php
    require_once "../config/connection.php";

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Location: index.php");
        exit;
    }

    $nombre           = trim($_POST["nombre"] ?? "");
    $apellido         = trim($_POST["apellido"] ?? "");
    $nacionalidad     = trim($_POST["nacionalidad"] ?? "");
    $fecha_nacimiento = trim($_POST["fecha_nacimiento"] ?? "") ?: null;

    if ($nombre === "" || $apellido === "") {
        header("Location: create.php?error=" . urlencode("El nombre y apellido son obligatorios"));
    }

    // Aquí va el código para crear el autor usando una consulta preparada
    $stmt = $conn->prepare("INSERT INTO autores (nombre, apellido, nacionalidad, fecha_nacimiento) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $apellido, $nacionalidad, $fecha_nacimiento);

    if ($stmt->execute()) {
        header("Location: index.php?success=" . urlencode("Autor creado exitosamente"));
    } else {
        header("Location: create.php?error=" . urlencode("Error al crear el autor: " . $stmt->error));
    }
    $stmt->close();
    $conn->close();
?>

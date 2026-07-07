<?php
    require_once "../config/connection.php";

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Location: index.php");
        exit;
    }

    $nombre    = trim($_POST["nombre"]    ?? "");
    $apellido  = trim($_POST["apellido"]  ?? "");
    $telefono  = trim($_POST["telefono"]  ?? "") ?: null;
    $correo    = trim($_POST["correo"]    ?? "") ?: null;
    $direccion = trim($_POST["direccion"] ?? "") ?: null;
    $categoria = trim($_POST["categoria"] ?? "General");

    if ($nombre === "" || $apellido === "") {
        header("Location: create.php?error=" . urlencode("El nombre y apellido son obligatorios"));
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO contactos (nombre, apellido, telefono, correo, direccion, categoria) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nombre, $apellido, $telefono, $correo, $direccion, $categoria);

    if ($stmt->execute()) {
        header("Location: index.php?success=" . urlencode("Contacto creado exitosamente"));
    } else {
        header("Location: create.php?error=" . urlencode("Error al crear el contacto: " . $stmt->error));
    }
    $stmt->close();
    $conn->close();
?>

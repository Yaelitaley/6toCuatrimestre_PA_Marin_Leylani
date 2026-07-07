<?php
    require_once "../config/connection.php";

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Location: index.php");
        exit;
    }

    $nombre    = trim($_POST["nombre"]    ?? "");
    $apellido  = trim($_POST["apellido"]  ?? "");
    $telefono  = trim($_POST["telefono"]  ?? "") ?: null;
    $email     = trim($_POST["email"]     ?? "") ?: null;
    $direccion = trim($_POST["direccion"] ?? "") ?: null;
    $notas     = trim($_POST["notas"]     ?? "") ?: null;

    if ($nombre === "" || $apellido === "") {
        header("Location: create.php?error=" . urlencode("El nombre y apellido son obligatorios"));
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO contactos (nombre, apellido, telefono, email, direccion, notas) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nombre, $apellido, $telefono, $email, $direccion, $notas);

    if ($stmt->execute()) {
        header("Location: index.php?success=" . urlencode("Contacto creado exitosamente"));
    } else {
        header("Location: create.php?error=" . urlencode("Error al crear el contacto: " . $stmt->error));
    }
    $stmt->close();
    $conn->close();
?>

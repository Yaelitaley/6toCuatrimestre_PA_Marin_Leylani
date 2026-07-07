<?php
    require_once "../config/connection.php";

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Location: index.php");
        exit;
    }

    $id        = intval($_POST["id"]        ?? 0);
    $nombre    = trim($_POST["nombre"]      ?? "");
    $apellido  = trim($_POST["apellido"]    ?? "");
    $telefono  = trim($_POST["telefono"]    ?? "") ?: null;
    $correo    = trim($_POST["correo"]      ?? "") ?: null;
    $direccion = trim($_POST["direccion"]   ?? "") ?: null;
    $categoria = trim($_POST["categoria"]   ?? "General");

    if ($id <= 0 || $nombre === "" || $apellido === "") {
        header("Location: index.php?error=" . urlencode("Datos inválidos"));
        exit;
    }

    $stmt = $conn->prepare("UPDATE contactos SET nombre = ?, apellido = ?, telefono = ?, correo = ?, direccion = ?, categoria = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $nombre, $apellido, $telefono, $correo, $direccion, $categoria, $id);

    if ($stmt->execute()) {
        header("Location: index.php?success=" . urlencode("Contacto actualizado exitosamente"));
        exit;
    } else {
        header("Location: index.php?error=" . urlencode("Error al actualizar: " . $stmt->error));
        exit;
    }
    $stmt->close();
    $conn->close();
?>

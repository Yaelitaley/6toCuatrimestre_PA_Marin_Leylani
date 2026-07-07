<?php
    require_once "../config/connection.php";

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Location: index.php");
        exit;
    }

    // Recibimos y sanitizamos todos los campos del formulario
    $id       = intval($_POST["id"]       ?? 0);
    $nombre   = trim($_POST["nombre"]     ?? "");
    $apellido = trim($_POST["apellido"]   ?? "");
    $correo   = trim($_POST["correo"]     ?? "");
    $password = trim($_POST["password"]   ?? ""); // Puede estar vacío si no quiere cambiarla
    $telefono = trim($_POST["telefono"]   ?? "");
    $id_rol   = intval($_POST["id_rol"]   ?? 0);
    $activo   = intval($_POST["activo"]   ?? 0); // 1 = activo, 0 = inactivo

    // Validamos los campos obligatorios
    if ($id <= 0 || $nombre === "" || $apellido === "" || $correo === "" || $id_rol <= 0) {
        header("Location: index.php?error=" . urlencode("Datos inválidos"));
        exit;
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        header("Location: edit.php?id=$id&error=" . urlencode("El correo electrónico no tiene un formato válido"));
        exit;
    }

    // Si el campo password viene vacío, actualizamos sin cambiar la contraseña.
    // Si viene con valor, la encriptamos y la incluimos en el UPDATE.
    if ($password !== "") {
        // Actualizamos todos los campos incluyendo la contraseña
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, correo = ?, password = ?, telefono = ?, id_rol = ?, activo = ? WHERE id = ?");
        // sssssiii: 3 strings (nombre, apellido, correo), 1 string (password), 1 string (telefono), 2 integers (id_rol, activo), 1 integer (id)
        $stmt->bind_param("sssssiii", $nombre, $apellido, $correo, $password_hash, $telefono, $id_rol, $activo, $id);
    } else {
        // Actualizamos todos los campos EXCEPTO la contraseña
        $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, correo = ?, telefono = ?, id_rol = ?, activo = ? WHERE id = ?");
        $stmt->bind_param("ssssiii", $nombre, $apellido, $correo, $telefono, $id_rol, $activo, $id);
    }

    if ($stmt->execute()) {
        header("Location: index.php?success=" . urlencode("Usuario actualizado exitosamente"));
        exit;
    } else {
        header("Location: index.php?error=" . urlencode("Error al actualizar el usuario: " . $stmt->error));
        exit;
    }

    $stmt->close();
    $conn->close();
?>

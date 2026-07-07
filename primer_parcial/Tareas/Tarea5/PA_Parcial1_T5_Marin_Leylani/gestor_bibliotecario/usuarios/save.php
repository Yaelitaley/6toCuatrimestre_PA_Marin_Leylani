<?php
    require_once "../config/connection.php"; // Importamos la conexión a la base de datos

    // Validamos que la petición sea de tipo POST.
    // Si alguien intenta acceder a este archivo directamente desde el navegador (GET), lo redirigimos.
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Location: index.php");
        exit;
    }

    // Recibimos y sanitizamos cada campo del formulario usando trim() para eliminar espacios innecesarios.
    // El operador ?? "" nos protege en caso de que el campo no exista en el POST.
    $nombre   = trim($_POST["nombre"]   ?? "");
    $apellido = trim($_POST["apellido"] ?? "");
    $correo   = trim($_POST["correo"]   ?? "");
    $password = trim($_POST["password"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $id_rol   = intval($_POST["id_rol"] ?? 0); // intval() convierte el valor a entero, protegiendo contra inyección en campos numéricos

    // Validamos que los campos obligatorios no estén vacíos antes de intentar insertar.
    if ($nombre === "" || $apellido === "" || $correo === "" || $password === "" || $id_rol <= 0) {
        header("Location: create.php?error=" . urlencode("Todos los campos obligatorios deben llenarse"));
        exit;
    }

    // Usamos filter_var() con FILTER_VALIDATE_EMAIL para verificar que el correo tenga un formato válido.
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        header("Location: create.php?error=" . urlencode("El correo electrónico no tiene un formato válido"));
        exit;
    }

    // Usamos password_hash() para encriptar la contraseña antes de guardarla en la base de datos.
    // PASSWORD_DEFAULT usa bcrypt, que es el algoritmo recomendado por PHP.
    // NUNCA se debe guardar una contraseña en texto plano.
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Preparamos la consulta INSERT con marcadores de posición (?) para cada valor.
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, correo, password, telefono, id_rol) VALUES (?, ?, ?, ?, ?, ?)");
    // bind_param recibe primero los tipos: s=string, i=integer.
    // El orden debe coincidir exactamente con los marcadores de posición de la consulta.
    $stmt->bind_param("sssssi", $nombre, $apellido, $correo, $password_hash, $telefono, $id_rol);

    if ($stmt->execute()) {
        header("Location: index.php?success=" . urlencode("Usuario registrado exitosamente"));
        exit;
    } else {
        header("Location: create.php?error=" . urlencode("Error al registrar el usuario: " . $stmt->error));
        exit;
    }

    $stmt->close(); // Cerramos la consulta preparada
    $conn->close(); // Cerramos la conexión a la base de datos
?>

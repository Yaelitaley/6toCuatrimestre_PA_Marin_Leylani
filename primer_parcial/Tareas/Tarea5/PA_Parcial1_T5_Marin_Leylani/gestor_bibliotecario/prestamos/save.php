<?php
    require_once "../config/connection.php";

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Location: index.php");
        exit;
    }

    // Recibimos y sanitizamos los campos del formulario
    $id_usuario      = intval($_POST["id_usuario"]      ?? 0);
    $fecha_prestamo  = trim($_POST["fecha_prestamo"]    ?? "");
    $fecha_devolucion = trim($_POST["fecha_devolucion"] ?? "");
    $estado          = trim($_POST["estado"]            ?? "");

    // Validamos que todos los campos obligatorios tengan valor
    if ($id_usuario <= 0 || $fecha_prestamo === "" || $fecha_devolucion === "" || $estado === "") {
        header("Location: create.php?error=" . urlencode("Todos los campos son obligatorios"));
        exit;
    }

    // Validamos que la fecha de devolución sea posterior a la fecha de préstamo.
    // strtotime() convierte una fecha en texto a un timestamp de Unix (número entero),
    // lo que nos permite comparar fechas fácilmente.
    if (strtotime($fecha_devolucion) <= strtotime($fecha_prestamo)) {
        header("Location: create.php?error=" . urlencode("La fecha de devolución debe ser posterior a la fecha de préstamo"));
        exit;
    }

    // Validamos que el estado sea uno de los valores permitidos por el ENUM de la base de datos
    $estados_validos = ["prestado", "devuelto", "retrasado"];
    if (!in_array($estado, $estados_validos)) {
        header("Location: create.php?error=" . urlencode("Estado no válido"));
        exit;
    }

    // Insertamos el préstamo usando prepared statement con marcadores de posición
    $stmt = $conn->prepare("INSERT INTO prestamos (id_usuario, fecha_prestamo, fecha_devolucion, estado) VALUES (?, ?, ?, ?)");
    // "isss": i=entero (id_usuario), s=string (fecha_prestamo), s=string (fecha_devolucion), s=string (estado)
    $stmt->bind_param("isss", $id_usuario, $fecha_prestamo, $fecha_devolucion, $estado);

    if ($stmt->execute()) {
        header("Location: index.php?success=" . urlencode("Préstamo registrado exitosamente"));
        exit;
    } else {
        header("Location: create.php?error=" . urlencode("Error al registrar el préstamo: " . $stmt->error));
        exit;
    }

    $stmt->close();
    $conn->close();
?>

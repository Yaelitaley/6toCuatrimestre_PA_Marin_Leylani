<?php
    require_once "../config/connection.php";

    $id = intval($_GET["id"] ?? 0);

    if ($id <= 0) {
        header("Location: index.php?error=" . urlencode("ID inválido"));
        exit;
    }

    // Antes de eliminar, verificamos que el usuario no tenga préstamos registrados.
    // Si tiene préstamos, la base de datos lanzaría un error por la llave foránea fk_prestamo_usuario.
    // Es mejor manejarlo nosotros con un mensaje claro antes de intentar el DELETE.
    $stmt_check = $conn->prepare("SELECT COUNT(*) AS total FROM prestamos WHERE id_usuario = ?");
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $check = $stmt_check->get_result()->fetch_assoc();
    $stmt_check->close();

    if ($check["total"] > 0) {
        header("Location: index.php?error=" . urlencode("No se puede eliminar: el usuario tiene préstamos registrados"));
        exit;
    }

    // Si no tiene préstamos, procedemos con el DELETE usando prepared statement
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id); // "i" indica que el parámetro es un entero
    if ($stmt->execute()) {
        header("Location: index.php?success=" . urlencode("Usuario eliminado exitosamente"));
        exit;
    } else {
        header("Location: index.php?error=" . urlencode("Error al eliminar el usuario: " . $stmt->error));
        exit;
    }
?>

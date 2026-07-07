<?php
    require_once "../config/connection.php";

    $id = intval($_GET["id"] ?? 0);

    if ($id <= 0) {
        header("Location: index.php?error=" . urlencode("ID inválido"));
        exit;
    }

    // Obtener nombre del contacto para mostrarlo en la confirmación
    $stmt = $conn->prepare("SELECT nombre, apellido FROM contactos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $contacto  = $resultado->fetch_assoc();
    $stmt->close();

    if (!$contacto) {
        header("Location: index.php?error=" . urlencode("Contacto no encontrado"));
        exit;
    }

    // Si se confirmó la eliminación
    if (isset($_GET["confirm"])) {
        $stmt = $conn->prepare("DELETE FROM contactos WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header("Location: index.php?success=" . urlencode("Contacto eliminado exitosamente"));
            exit;
        } else {
            header("Location: index.php?error=" . urlencode("Error al eliminar el contacto: " . $stmt->error));
            exit;
        }
        $stmt->close();
        $conn->close();
    }
?>
<?php include_once "../includes/header.php"; ?>

<div class="page-header">
    <h1><i class="fas fa-trash"></i> Eliminar Contacto</h1>
    <a href="index.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<div class="card">
    <div class="confirm-box">
        <i class="fas fa-exclamation-triangle"></i>
        <h3>¿Estás segura?</h3>
        <p>
            Estás a punto de eliminar al contacto
            <strong><?= htmlspecialchars($contacto["nombre"] . " " . $contacto["apellido"]) ?></strong>.
            Esta acción no se puede deshacer.
        </p>
        <div class="form-actions">
            <a href="delete.php?id=<?= urlencode($id) ?>&confirm=1" class="btn btn-danger">
                <i class="fas fa-trash"></i> Sí, eliminar
            </a>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </div>
    </div>
</div>

<?php include_once "../includes/footer.php"; ?>

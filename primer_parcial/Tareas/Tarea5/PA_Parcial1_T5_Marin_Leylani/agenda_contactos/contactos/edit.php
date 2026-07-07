<?php
    require_once "../config/connection.php";

    $id = intval($_GET["id"] ?? 0);

    if ($id > 0) {
        $stmt = $conn->prepare("SELECT id, nombre, apellido, telefono, email, direccion, notas FROM contactos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $contacto  = $resultado->fetch_assoc();
        $stmt->close();
        $conn->close();

        if (!$contacto) {
            header("Location: index.php?error=" . urlencode("Contacto no encontrado"));
            exit;
        }
    } else {
        header("Location: index.php?error=" . urlencode("ID inválido"));
        exit;
    }
?>
<?php include_once "../includes/header.php"; ?>

<div class="page-header">
    <h1><i class="fas fa-user-edit"></i> Editar Contacto</h1>
    <a href="index.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<div class="card">
    <form action="update.php" method="POST">
        <input type="hidden" name="id" value="<?= $contacto["id"] ?>">

        <div class="form-group">
            <label><i class="fas fa-user"></i> Nombre <span style="color:#dc2626">*</span></label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($contacto["nombre"]) ?>" required>
        </div>

        <div class="form-group">
            <label><i class="fas fa-user"></i> Apellido <span style="color:#dc2626">*</span></label>
            <input type="text" name="apellido" value="<?= htmlspecialchars($contacto["apellido"]) ?>" required>
        </div>

        <div class="form-group">
            <label><i class="fas fa-phone"></i> Teléfono</label>
            <input type="text" name="telefono" value="<?= htmlspecialchars($contacto["telefono"] ?? "") ?>">
        </div>

        <div class="form-group">
            <label><i class="fas fa-envelope"></i> Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($contacto["email"] ?? "") ?>">
        </div>

        <div class="form-group">
            <label><i class="fas fa-map-marker-alt"></i> Dirección</label>
            <input type="text" name="direccion" value="<?= htmlspecialchars($contacto["direccion"] ?? "") ?>">
        </div>

        <div class="form-group">
            <label><i class="fas fa-sticky-note"></i> Notas</label>
            <textarea name="notas"><?= htmlspecialchars($contacto["notas"] ?? "") ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Actualizar
            </button>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </div>
    </form>
</div>

<?php include_once "../includes/footer.php"; ?>

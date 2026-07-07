<?php
    require_once "../config/connection.php";

    $id = intval($_GET["id"] ?? 0);

    // Aquí va el código para obtener el autor usando una consulta preparada
    if ($id > 0) {
        $stmt = $conn->prepare("SELECT id, nombre, apellido, nacionalidad, fecha_nacimiento FROM autores WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $autor = $resultado->fetch_assoc();
        $stmt->close();
        $conn->close();
    } else {
        header("Location: index.php?error=" . urlencode("ID inválido"));
        exit;
    }
?>
<?php include_once "../includes/header.php"; ?>

<div class="page-header">
    <h1><i class="fas fa-user-edit"></i> Editar Autor</h1>
    <a href="index.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<div class="card">
    <form action="update.php" method="POST">
        <input type="hidden" name="id" value="<?= $autor["id"] ?? $id ?>">

        <div class="form-group">
            <label><i class="fas fa-user"></i> Nombre</label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($autor["nombre"] ?? "") ?>" required>
        </div>

        <div class="form-group">
            <label><i class="fas fa-user"></i> Apellido</label>
            <input type="text" name="apellido" value="<?= htmlspecialchars($autor["apellido"] ?? "") ?>" required>
        </div>

        <div class="form-group">
            <label><i class="fas fa-globe"></i> Nacionalidad</label>
            <input type="text" name="nacionalidad" value="<?= htmlspecialchars($autor["nacionalidad"] ?? "") ?>">
        </div>

        <div class="form-group">
            <label><i class="fas fa-calendar"></i> Fecha de Nacimiento</label>
            <input type="date" name="fecha_nacimiento" value="<?= $autor["fecha_nacimiento"] ?? "" ?>">
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

<?php
    require_once "../config/connection.php";

    $id = intval($_GET["id"] ?? 0);

    if ($id > 0) {
        // Obtenemos los datos del préstamo que queremos editar
        $stmt = $conn->prepare("SELECT id, id_usuario, fecha_prestamo, fecha_devolucion, estado FROM prestamos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $prestamo = $resultado->fetch_assoc();
        $stmt->close();

        if (!$prestamo) {
            header("Location: index.php?error=" . urlencode("Préstamo no encontrado"));
            exit;
        }

        // También cargamos los usuarios activos para el select
        $stmt_usuarios = $conn->prepare("SELECT id, CONCAT(nombre, ' ', apellido) AS nombre_completo FROM usuarios WHERE activo = 1");
        $stmt_usuarios->execute();
        $usuarios = $stmt_usuarios->get_result();
        $stmt_usuarios->close();
        $conn->close();
    } else {
        header("Location: index.php?error=" . urlencode("ID inválido"));
        exit;
    }
?>
<?php include_once "../includes/header.php"; ?>

<div class="page-header">
    <h1><i class="fas fa-edit"></i> Editar Préstamo</h1>
    <a href="index.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<div class="card">
    <form action="update.php" method="POST">
        <!-- Campo oculto para pasar el ID del préstamo al script de actualización -->
        <input type="hidden" name="id" value="<?= $prestamo["id"] ?>">

        <div class="form-group">
            <label><i class="fas fa-user"></i> Usuario</label>
            <select name="id_usuario" required>
                <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                    <option value="<?= $usuario["id"] ?>"
                        <?= $usuario["id"] == $prestamo["id_usuario"] ? "selected" : "" ?>>
                        <!-- "selected" marca el usuario que ya tiene asignado este préstamo -->
                        <?= htmlspecialchars($usuario["nombre_completo"]) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label><i class="fas fa-calendar-alt"></i> Fecha de Préstamo</label>
            <input type="date" name="fecha_prestamo" value="<?= htmlspecialchars($prestamo["fecha_prestamo"]) ?>" required>
        </div>

        <div class="form-group">
            <label><i class="fas fa-calendar-check"></i> Fecha de Devolución</label>
            <input type="date" name="fecha_devolucion" value="<?= htmlspecialchars($prestamo["fecha_devolucion"]) ?>" required>
        </div>

        <div class="form-group">
            <label><i class="fas fa-info-circle"></i> Estado</label>
            <select name="estado" required>
                <!-- Para cada opción, comparamos su valor con el estado actual del préstamo para marcarlo como selected -->
                <option value="prestado"  <?= $prestamo["estado"] === "prestado"  ? "selected" : "" ?>>Prestado</option>
                <option value="devuelto"  <?= $prestamo["estado"] === "devuelto"  ? "selected" : "" ?>>Devuelto</option>
                <option value="retrasado" <?= $prestamo["estado"] === "retrasado" ? "selected" : "" ?>>Retrasado</option>
            </select>
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

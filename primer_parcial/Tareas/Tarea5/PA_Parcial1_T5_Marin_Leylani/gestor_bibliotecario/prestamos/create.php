<?php
    require_once "../config/connection.php";

    // Cargamos la lista de usuarios activos para el select del formulario.
    // Solo mostramos usuarios con activo = 1 ya que no tendría sentido prestar a un usuario inactivo.
    $stmt = $conn->prepare("SELECT id, CONCAT(nombre, ' ', apellido) AS nombre_completo FROM usuarios WHERE activo = 1");
    $stmt->execute();
    $usuarios = $stmt->get_result();
    $stmt->close();
    $conn->close();
?>
<?php include_once "../includes/header.php"; ?>

<div class="page-header">
    <h1><i class="fas fa-plus-circle"></i> Nuevo Préstamo</h1>
    <a href="index.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<?php if (isset($_GET["error"])): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <?= htmlspecialchars($_GET["error"]) ?>
    </div>
<?php endif; ?>

<div class="card">
    <form action="save.php" method="POST">
        <div class="form-group">
            <label><i class="fas fa-user"></i> Usuario</label>
            <select name="id_usuario" required>
                <option value="">-- Selecciona un usuario --</option>
                <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                    <option value="<?= $usuario["id"] ?>">
                        <?= htmlspecialchars($usuario["nombre_completo"]) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label><i class="fas fa-calendar-alt"></i> Fecha de Préstamo</label>
            <!-- value con date() pre-rellena el campo con la fecha de hoy -->
            <input type="date" name="fecha_prestamo" value="<?= date('Y-m-d') ?>" required>
        </div>

        <div class="form-group">
            <label><i class="fas fa-calendar-check"></i> Fecha de Devolución</label>
            <input type="date" name="fecha_devolucion" required>
        </div>

        <div class="form-group">
            <label><i class="fas fa-info-circle"></i> Estado</label>
            <select name="estado" required>
                <option value="prestado">Prestado</option>
                <option value="devuelto">Devuelto</option>
                <option value="retrasado">Retrasado</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Guardar
            </button>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </div>
    </form>
</div>

<?php include_once "../includes/footer.php"; ?>

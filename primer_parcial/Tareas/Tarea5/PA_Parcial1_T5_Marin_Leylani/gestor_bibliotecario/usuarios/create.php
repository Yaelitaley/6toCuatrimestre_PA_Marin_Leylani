<?php
    require_once "../config/connection.php"; // Necesitamos la conexión para cargar los roles en el select

    // Obtenemos todos los roles disponibles para mostrarlos en el formulario.
    // El usuario deberá seleccionar uno al momento de registrarse.
    $stmt = $conn->prepare("SELECT id, nombre FROM roles");
    $stmt->execute();
    $roles = $stmt->get_result();
    $stmt->close();
    $conn->close();
?>
<?php include_once "../includes/header.php"; ?>

<div class="page-header">
    <h1><i class="fas fa-user-plus"></i> Nuevo Usuario</h1>
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
            <label><i class="fas fa-user"></i> Nombre</label>
            <input type="text" name="nombre" placeholder="Ej: Juan" required>
        </div>

        <div class="form-group">
            <label><i class="fas fa-user"></i> Apellido</label>
            <input type="text" name="apellido" placeholder="Ej: Pérez" required>
        </div>

        <div class="form-group">
            <label><i class="fas fa-envelope"></i> Correo electrónico</label>
            <input type="email" name="correo" placeholder="Ej: juan@biblioteca.com" required>
        </div>

        <div class="form-group">
            <label><i class="fas fa-lock"></i> Contraseña</label>
            <input type="password" name="password" placeholder="Contraseña" required>
        </div>

        <div class="form-group">
            <label><i class="fas fa-phone"></i> Teléfono</label>
            <input type="text" name="telefono" placeholder="Ej: 9811234567">
        </div>

        <div class="form-group">
            <label><i class="fas fa-id-badge"></i> Rol</label>
            <!-- Generamos el select de roles dinámicamente con los datos de la base de datos -->
            <select name="id_rol" required>
                <option value="">-- Selecciona un rol --</option>
                <?php while ($rol = $roles->fetch_assoc()): ?>
                    <option value="<?= $rol["id"] ?>">
                        <?= htmlspecialchars($rol["nombre"]) ?>
                    </option>
                <?php endwhile; ?>
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

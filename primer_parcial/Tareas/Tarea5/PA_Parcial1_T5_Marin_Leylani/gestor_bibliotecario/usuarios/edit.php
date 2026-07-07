<?php
    require_once "../config/connection.php";

    // Obtenemos el ID del usuario desde la URL mediante GET y lo convertimos a entero
    $id = intval($_GET["id"] ?? 0);

    if ($id > 0) {
        // Primero buscamos los datos del usuario que queremos editar
        $stmt = $conn->prepare("SELECT id, nombre, apellido, correo, telefono, id_rol, activo FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc(); // Obtenemos la fila como array asociativo
        $stmt->close();

        if (!$usuario) { // Si no existe ningún usuario con ese ID, redirigimos con error
            header("Location: index.php?error=" . urlencode("Usuario no encontrado"));
            exit;
        }

        // Luego cargamos todos los roles disponibles para el select del formulario
        $stmt_roles = $conn->prepare("SELECT id, nombre FROM roles");
        $stmt_roles->execute();
        $roles = $stmt_roles->get_result();
        $stmt_roles->close();
        $conn->close();
    } else {
        header("Location: index.php?error=" . urlencode("ID inválido"));
        exit;
    }
?>
<?php include_once "../includes/header.php"; ?>

<div class="page-header">
    <h1><i class="fas fa-user-edit"></i> Editar Usuario</h1>
    <a href="index.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<div class="card">
    <form action="update.php" method="POST">
        <!-- Campo oculto para enviar el ID del usuario al script de actualización -->
        <input type="hidden" name="id" value="<?= $usuario["id"] ?>">

        <div class="form-group">
            <label><i class="fas fa-user"></i> Nombre</label>
            <!-- value="..." pre-rellena el campo con el dato actual del usuario -->
            <input type="text" name="nombre" value="<?= htmlspecialchars($usuario["nombre"]) ?>" required>
        </div>

        <div class="form-group">
            <label><i class="fas fa-user"></i> Apellido</label>
            <input type="text" name="apellido" value="<?= htmlspecialchars($usuario["apellido"]) ?>" required>
        </div>

        <div class="form-group">
            <label><i class="fas fa-envelope"></i> Correo electrónico</label>
            <input type="email" name="correo" value="<?= htmlspecialchars($usuario["correo"]) ?>" required>
        </div>

        <div class="form-group">
            <label><i class="fas fa-lock"></i> Nueva contraseña <small>(dejar vacío para no cambiarla)</small></label>
            <input type="password" name="password" placeholder="Nueva contraseña (opcional)">
        </div>

        <div class="form-group">
            <label><i class="fas fa-phone"></i> Teléfono</label>
            <input type="text" name="telefono" value="<?= htmlspecialchars($usuario["telefono"]) ?>">
        </div>

        <div class="form-group">
            <label><i class="fas fa-id-badge"></i> Rol</label>
            <select name="id_rol" required>
                <?php while ($rol = $roles->fetch_assoc()): ?>
                    <option value="<?= $rol["id"] ?>"
                        <?= $rol["id"] == $usuario["id_rol"] ? "selected" : "" ?>>
                        <!-- selected marca la opción que ya tiene el usuario actualmente -->
                        <?= htmlspecialchars($rol["nombre"]) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label><i class="fas fa-toggle-on"></i> Estado</label>
            <select name="activo">
                <!-- Marcamos como seleccionada la opción que corresponde al estado actual del usuario -->
                <option value="1" <?= $usuario["activo"] ? "selected" : "" ?>>Activo</option>
                <option value="0" <?= !$usuario["activo"] ? "selected" : "" ?>>Inactivo</option>
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

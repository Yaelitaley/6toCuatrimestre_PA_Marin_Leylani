<?php include_once "../includes/header.php"; ?>

<div class="page-header">
    <h1><i class="fas fa-user-plus"></i> Nuevo Contacto</h1>
    <a href="index.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<?php if (isset($_GET["error"])): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <?= htmlspecialchars($_GET["error"]) ?>
    </div>
<?php endif; ?>

<div class="card">
    <form action="save.php" method="POST">
        <div class="form-group">
            <label><i class="fas fa-user"></i> Nombre <span style="color:#dc2626">*</span></label>
            <input type="text" name="nombre" placeholder="Ej: María" required>
        </div>

        <div class="form-group">
            <label><i class="fas fa-user"></i> Apellido <span style="color:#dc2626">*</span></label>
            <input type="text" name="apellido" placeholder="Ej: González" required>
        </div>

        <div class="form-group">
            <label><i class="fas fa-phone"></i> Teléfono</label>
            <input type="text" name="telefono" placeholder="Ej: 555-0101">
        </div>

        <div class="form-group">
            <label><i class="fas fa-envelope"></i> Correo</label>
            <input type="email" name="correo" placeholder="Ej: ejemplo@correo.com">
        </div>

        <div class="form-group">
            <label><i class="fas fa-map-marker-alt"></i> Dirección</label>
            <input type="text" name="direccion" placeholder="Ej: Av. Principal 123">
        </div>

        <div class="form-group">
            <label><i class="fas fa-tag"></i> Categoría</label>
            <select name="categoria">
                <option value="General">General</option>
                <option value="Familiar">Familiar</option>
                <option value="Trabajo">Trabajo</option>
                <option value="Amigos">Amigos</option>
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

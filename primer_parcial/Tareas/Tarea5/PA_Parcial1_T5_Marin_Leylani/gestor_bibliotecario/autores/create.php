<?php include_once "../includes/header.php"; ?>

<div class="page-header">
    <h1><i class="fas fa-user-plus"></i> Nuevo Autor</h1>
    <a href="index.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<div class="card">
    <form action="save.php" method="POST">
        <div class="form-group">
            <label><i class="fas fa-user"></i> Nombre</label>
            <input type="text" name="nombre" placeholder="Ej: Gabriel" required>
        </div>

        <div class="form-group">
            <label><i class="fas fa-user"></i> Apellido</label>
            <input type="text" name="apellido" placeholder="Ej: García Márquez" required>
        </div>

        <div class="form-group">
            <label><i class="fas fa-globe"></i> Nacionalidad</label>
            <input type="text" name="nacionalidad" placeholder="Ej: Colombiana">
        </div>

        <div class="form-group">
            <label><i class="fas fa-calendar"></i> Fecha de Nacimiento</label>
            <input type="date" name="fecha_nacimiento">
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

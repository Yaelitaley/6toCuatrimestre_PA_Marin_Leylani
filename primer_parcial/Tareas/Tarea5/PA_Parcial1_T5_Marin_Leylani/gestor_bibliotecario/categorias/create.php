<?php include_once "../includes/header.php"; ?>

<div class="page-header">
    <h1><i class="fas fa-plus-circle"></i> Nueva Categoría</h1>
    <a href="index.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<div class="card">
    <form action="save.php" method="POST">
        <div class="form-group">
            <label><i class="fas fa-tag"></i> Nombre</label>
            <input type="text" name="nombre" placeholder="Ej: Programación" required>
        </div>

        <div class="form-group">
            <label><i class="fas fa-align-left"></i> Descripción</label>
            <textarea name="descripcion" placeholder="Descripción de la categoría (opcional)"></textarea>
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

<?php
    require_once "../config/connection.php";

    // Aquí va el código para obtener todas las categorías usando una consulta preparada
    $stmt = $conn->prepare("SELECT id, nombre FROM categorias ORDER BY nombre"); // Preparamos la consulta SQL para obtener las categorías
    $stmt->execute(); // Ejecutamos la consulta
    $resultado_categorias = $stmt->get_result(); // Obtengo el resultado de la consulta y lo guardo en una variable
    $categorias = []; // Creo un array vacío para guardar las categorías obtenidas de la consulta. ¿Qué pasa si no declaro este array antes del while? En primera, el arreglo no existirá más que en el ciclo. Es decir que será una variable local.
    while($row = $resultado_categorias->fetch_assoc()){ // Recorro el resultado de la consulta usando un while y guardo cada fila en un array llamado $categorias. El método fetch_assoc() devuelve cada fila como un array asociativo, donde las claves son los nombres de las columnas de la tabla.
        $categorias[] = $row; // Arreglo categorias en el cual se guarda las filas obtenidas de la consulta
    }
    $stmt->close(); // Cerramos la consulta preparada para liberar recursos
    
    // Aquí va el código para obtener todos los autores usando una consulta preparada
    $stmt = $conn->prepare("SELECT id, nombre, apellido FROM autores ORDER BY nombre"); // Preparmamos la consulta SQL para obtener los autores
    $stmt->execute(); // Ejecutamos la consulta
    $resultado_autores = $stmt->get_result(); // Obtengo el resultado de la consulta y lo guardo en una variable
    $autores = []; // Creo un array vacío para guardar los autores obtenidos.
    while($row = $resultado_autores->fetch_assoc()){
        $autores[] = $row; // Arreglo autores en el cual se guarda las filas obtenidas de la consulta
    }
    $stmt->close(); // Cerramos la consulta preparada para liberar recursos
?>
<?php include_once "../includes/header.php"; ?>

<div class="page-header">
    <h1><i class="fas fa-book-medical"></i> Nuevo Libro</h1>
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
            <label><i class="fas fa-book"></i> Título</label>
            <input type="text" name="titulo" placeholder="Ej: Cien años de soledad" required>
        </div>

        <div class="form-group">
            <label><i class="fas fa-barcode"></i> ISBN</label>
            <input type="text" name="isbn" placeholder="Ej: 978-0-06-112008-4">
        </div>

        <div class="form-group">
            <label><i class="fas fa-calendar"></i> Año de Publicación</label>
            <input type="number" name="anio_publicacion" placeholder="Ej: 1967" min="1000" max="<?= date('Y') ?>">
        </div>

        <div class="form-group">
            <label><i class="fas fa-building"></i> Editorial</label>
            <input type="text" name="editorial" placeholder="Ej: Editorial Sudamericana">
        </div>

        <div class="form-group">
            <label><i class="fas fa-cubes"></i> Cantidad</label>
            <input type="number" name="cantidad" placeholder="Ej: 5" min="0" value="0">
        </div>

        <div class="form-group">
            <label><i class="fas fa-check-circle"></i> Disponibles</label>
            <input type="number" name="disponibles" placeholder="Ej: 5" min="0" value="0">
        </div>

        <div class="form-group">
            <label><i class="fas fa-tag"></i> Categoría</label>
            <select name="id_categoria" required>
                <option value="">-- Selecciona una categoría --</option>
                <?php
                    // Aquí va el código para mostrar las categorías en el <select>
                    foreach($categorias as $categoria){ // Recorremos el arreglo con un ciclo for each, para llenar el select con las categorías obtenidas de la consulta.
                        echo "<option value='{$categoria['id']}'>{$categoria['nombre']}</option>"; // Imprimimos cada categoría como una opción del select, usando el id como valor y el nombre como texto visible.
                    }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label><i class="fas fa-user"></i> Autor(es)</label>
            <select name="id_autores[]" multiple required>
                <?php
                    // Aquí va el código para mostrar los autores en el <select>
                    foreach($autores as $autor){
                        echo "<option value='{$autor['id']}'>{$autor['nombre']} {$autor['apellido']}</option>"; // Imprimimos cada autor como una opción del select, usando el id como valor y el nombre completo como texto visible.
                    }
                ?>
            </select>
            <small>Mantén Ctrl (o Cmd) para seleccionar varios autores</small>
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

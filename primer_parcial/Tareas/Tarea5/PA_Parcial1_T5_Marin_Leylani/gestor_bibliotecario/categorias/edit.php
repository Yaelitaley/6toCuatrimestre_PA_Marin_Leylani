<?php
    require_once "../config/connection.php";

    $id = intval($_GET["id"] ?? 0); // Recibimos el ID de la categoría desde la URL, mediante el método GET. Si no se proporciona un ID válido, se asigna el valor de 0. Y con intval() nos aseguramos que el valor sea un número entero.

    // Aquí va el código para obtener la categoría usando una consulta preparada
    if($id > 0){
        $stmt = $conn->prepare("SELECT id, nombre, descripcion FROM categorias WHERE id = ?"); // Nuestra consulta preparada es un SELECT en el cual obtendremos los datos según el ID que recibimos por la URL. Recordemos que el signo de interrogación (?) es un marcador de posición para el valor que se pasará posteriormente. ¿Por qué primero hacemos un SELECT?
        $stmt->bind_param("i", $id); // Lo que hacemos es decirle a la consulta que el marcador de posición es un número entero (i) y que el valor que pasará es el de la variable $id.
        $stmt->execute(); // Ejecutamos la consulta preparada.
        $resultado = $stmt->get_result(); // Obtenemos el resultado de la consulta
        $categoria = $resultado->fetch_assoc(); // Obtenemos la fila del resultado como un array asociativo y lo almacenamos en la variable $categoria.
        $stmt->close(); // Cerramos la consulta preparada para liberar recursos.
        $conn->close(); // Cerramos la conexión a la base de datos para liberar recursos
    }else{
        header("Location: index.php?error=" . urlencode("ID inválido"));
        exit;
    }
?>

<?php include_once "../includes/header.php"; ?>

<div class="page-header">
    <h1><i class="fas fa-edit"></i> Editar Categoría</h1>
    <a href="index.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<div class="card">
    <form action="update.php" method="POST">
        <input type="hidden" name="id" value="<?= $categoria["id"] ?? $id ?>">

        <div class="form-group">
            <label><i class="fas fa-tag"></i> Nombre</label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($categoria["nombre"] ?? "") ?>" required>
        </div>

        <div class="form-group">
            <label><i class="fas fa-align-left"></i> Descripción</label>
            <textarea name="descripcion"><?= htmlspecialchars($categoria["descripcion"] ?? "") ?></textarea>
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

<?php
    require_once "../config/connection.php"; // Incluimos la conexión a la base de datos
?>
<?php include_once "../includes/header.php"; // Incluimos el encabezado. Básicamente lo que hace esto es que el archivo header.php se incrusta en mi archivo actual, el archivo header.php ya contiene el código HTML del encabezado, lo que me permitirá reutilizarlo cuantas veces sea necesario?> 

<div class="page-header">
    <h1><i class="fas fa-tags"></i> Categorías</h1>
    <a href="create.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nueva Categoría
    </a>
</div>

<?php if (isset($_GET["success"])): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?= htmlspecialchars($_GET["success"]) ?>
    </div>
<?php elseif (isset($_GET["error"])): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <?= htmlspecialchars($_GET["error"]) ?>
    </div>
<?php endif; ?>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
                // Aquí va el código para el select que mostrará las categorías
                $stmt = $conn->prepare("SELECT id, nombre, descripcion FROM categorias"); // En esta línea preparamos una consulta SQL, para mostrar las categorías. Esto lo hacemos mediante una consulta "SELECT", la cual seleccionará los campos "id", "nombre" y "descripcion" de la tabla "categorías".
                $stmt->execute(); // En esta linea, la función execute() ejecutamos la consulta que previamente preparamos.
                $resultado = $stmt->get_result(); // En la variable resultado, almacenamos el resultado de la consulta que ejecutamos. Hacemos utilizando la función o método get_result() el cual nos devuelve un objeto que representa el conjunto de resultados obtenido de la consulta.
                while($row = $resultado->fetch_assoc()){ // En esta línea de código, iniciamos un ciclo while para recorrer cada fila del resultado obtenido. Utilizamos una variable llamada $row para almacenar cada fila, y por medio de la función fetch_assoc() obtenemos cada fila como si fuera un array asociativo, lo cual permitirá que accedamos a los valores mediante clave-valor.
                    echo "<tr>"; // La etiqueta <tr> se utiliza para definir una fila en una tabla HTML.
                    echo "<td>" . htmlspecialchars($row["id"]) . "</td>"; // Utilizamos la etiqueta <td> para definir una celda dentro de la fila. En ella mostraremos el ID de la categooría y usaremos htmlspecialchars() para sanitizar el valor y evitar posibles inyecciones SQL.
                    echo "<td>" . htmlspecialchars($row["nombre"]) . "</td>";  // Insertamos el valor del nombre de la categoría.
                    echo "<td>" . htmlspecialchars($row["descripcion"]) . "</td>";  // Insertamos el valor del nombre de la descripcion.
                    echo "<td>"; // Abrimos una nueva celda para colocar los botones de acción (editar y eliminar).
                    echo "<a href='edit.php?id=" . urlencode($row["id"]) . "' class='btn btn-sm btn-warning'><i class='fas fa-edit'></i> Editar</a>";
                    echo "<a href='delete.php?id=" . urlencode($row["id"]) . "' class='btn btn-sm btn-danger'><i class='fas fa-trash'></i> Eliminar</a>";
                    echo "</td>";
                    echo "</tr>"; // Cerramos la etiqueta <tr> para finalizar la fila.
                }
                $stmt->close(); // Cerramos la consulta preparada para liberar recursos.
                $conn->close(); // Cerramos la conexión a la base de datos para liberar recursos.
            ?>
        </tbody>
    </table>
</div>

<?php include_once "../includes/footer.php"; ?>

<?php
    require_once "../config/connection.php"; // Incluimos la conexión a la base de datos
?>
<?php include_once "../includes/header.php"; ?>

<div class="page-header">
    <h1><i class="fas fa-users"></i> Usuarios</h1>
    <a href="create.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nuevo Usuario
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
                <th>Apellido</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Rol</th>
                <th>Activo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
                // Hacemos un JOIN con la tabla roles para obtener el nombre del rol en lugar del id_rol.
                // Así en lugar de mostrar "1" o "2", mostramos "Administrador" o "Bibliotecario".
                $stmt = $conn->prepare("
                    SELECT u.id, u.nombre, u.apellido, u.correo, u.telefono, r.nombre AS rol, u.activo
                    FROM usuarios u
                    INNER JOIN roles r ON u.id_rol = r.id
                ");
                $stmt->execute(); // Ejecutamos la consulta preparada
                $resultado = $stmt->get_result(); // Obtenemos el conjunto de resultados

                while ($row = $resultado->fetch_assoc()) { // Recorremos cada fila del resultado
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["nombre"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["apellido"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["correo"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["telefono"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["rol"]) . "</td>";
                    // El campo "activo" es BOOLEAN en la base de datos (0 o 1).
                    // Usamos un operador ternario para mostrar "Sí" si es 1, o "No" si es 0.
                    echo "<td>" . ($row["activo"] ? "Sí" : "No") . "</td>";
                    echo "<td>";
                    echo "<a href='edit.php?id=" . urlencode($row["id"]) . "' class='btn btn-sm btn-warning'><i class='fas fa-edit'></i> Editar</a> ";
                    echo "<a href='delete.php?id=" . urlencode($row["id"]) . "' class='btn btn-sm btn-danger'><i class='fas fa-trash'></i> Eliminar</a>";
                    echo "</td>";
                    echo "</tr>";
                }
                $stmt->close(); // Cerramos la consulta preparada para liberar recursos
                $conn->close(); // Cerramos la conexión a la base de datos para liberar recursos
            ?>
        </tbody>
    </table>
</div>

<?php include_once "../includes/footer.php"; ?>

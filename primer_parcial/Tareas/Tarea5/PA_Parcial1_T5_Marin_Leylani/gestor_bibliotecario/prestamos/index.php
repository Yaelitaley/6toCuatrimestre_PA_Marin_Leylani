<?php
    require_once "../config/connection.php";
?>
<?php include_once "../includes/header.php"; ?>

<div class="page-header">
    <h1><i class="fas fa-hand-holding-heart"></i> Préstamos</h1>
    <a href="create.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nuevo Préstamo
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
                <th>Usuario</th>
                <th>Fecha Préstamo</th>
                <th>Fecha Devolución</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
                // Usamos JOIN para mostrar el nombre completo del usuario en lugar de solo su id_usuario.
                // Así la tabla es mucho más legible para quien administra el sistema.
                $stmt = $conn->prepare("
                    SELECT p.id, CONCAT(u.nombre, ' ', u.apellido) AS usuario,
                           p.fecha_prestamo, p.fecha_devolucion, p.estado
                    FROM prestamos p
                    INNER JOIN usuarios u ON p.id_usuario = u.id
                    ORDER BY p.fecha_prestamo DESC
                ");
                $stmt->execute();
                $resultado = $stmt->get_result();

                while ($row = $resultado->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["usuario"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["fecha_prestamo"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["fecha_devolucion"]) . "</td>";
                    // Mostramos el estado con un estilo visual diferente según el valor
                    $estado = htmlspecialchars($row["estado"]);
                    $clase_estado = "";
                    if ($estado === "prestado")   $clase_estado = "color: orange;";
                    if ($estado === "devuelto")   $clase_estado = "color: green;";
                    if ($estado === "retrasado")  $clase_estado = "color: red;";
                    echo "<td><strong style='$clase_estado'>" . ucfirst($estado) . "</strong></td>";
                    echo "<td>";
                    echo "<a href='edit.php?id=" . urlencode($row["id"]) . "' class='btn btn-sm btn-warning'><i class='fas fa-edit'></i> Editar</a> ";
                    echo "<a href='delete.php?id=" . urlencode($row["id"]) . "' class='btn btn-sm btn-danger'><i class='fas fa-trash'></i> Eliminar</a>";
                    echo "</td>";
                    echo "</tr>";
                }
                $stmt->close();
                $conn->close();
            ?>
        </tbody>
    </table>
</div>

<?php include_once "../includes/footer.php"; ?>

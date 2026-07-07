<?php
    require_once "../config/connection.php";
?>
<?php include_once "../includes/header.php"; ?>

<div class="page-header">
    <h1><i class="fas fa-book"></i> Libros</h1>
    <a href="create.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nuevo Libro
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
                <th>Título</th>
                <th>ISBN</th>
                <th>Categoría</th>
                <th>Año</th>
                <th>Editorial</th>
                <th>Cantidad</th>
                <th>Acciones</th>
                <th>Disponibles</th>
            </tr>
        </thead>
        <tbody>
            <?php
                // Aquí va el código para el select que mostrará los libros
                 $stmt = $conn->prepare("SELECT id, titulo, isbn, anio_publicacion, editorial, cantidad, disponibles FROM libros");
                $stmt->execute();
                $resultado = $stmt->get_result();
                while ($row = $resultado->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["titulo"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["isbn"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["anio_publicacion"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["editorial"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["cantidad"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["disponibles"]) . "</td>";
                    echo "<td>";
                    echo "<a href='edit.php?id=" . urlencode($row["id"]) . "' class='btn btn-sm btn-warning'><i class='fas fa-edit'></i> Editar</a>";
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

<?php
    require_once "../config/connection.php";
?>
<?php include_once "../includes/header.php"; ?>

<div class="page-header">
    <h1><i class="fas fa-users"></i> Contactos</h1>
    <a href="create.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nuevo Contacto
    </a>
</div>

<?php if (isset($_GET["success"])): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?= htmlspecialchars($_GET["success"]) ?>
    </div>
<?php elseif (isset($_GET["error"])): ?>
    <div class="alert alert-error">
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
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Dirección</th>
                <th>Categoría</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $stmt = $conn->prepare("SELECT id, nombre, apellido, telefono, correo, direccion, categoria FROM contactos ORDER BY apellido, nombre");
                $stmt->execute();
                $resultado = $stmt->get_result();

                if ($resultado->num_rows === 0):
            ?>
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <i class="fas fa-address-book"></i>
                            <p>No hay contactos registrados aún.</p>
                        </div>
                    </td>
                </tr>
            <?php
                else:
                    while ($row = $resultado->fetch_assoc()):
            ?>
                <tr>
                    <td><?= htmlspecialchars($row["id"]) ?></td>
                    <td><?= htmlspecialchars($row["nombre"]) ?></td>
                    <td><?= htmlspecialchars($row["apellido"]) ?></td>
                    <td><?= htmlspecialchars($row["telefono"] ?? "—") ?></td>
                    <td><?= htmlspecialchars($row["correo"] ?? "—") ?></td>
                    <td><?= htmlspecialchars($row["direccion"] ?? "—") ?></td>
                    <td><?= htmlspecialchars($row["categoria"] ?? "—") ?></td>
                    <td>
                        <div class="actions">
                            <a href="edit.php?id=<?= urlencode($row["id"]) ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <a href="delete.php?id=<?= urlencode($row["id"]) ?>" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i> Eliminar
                            </a>
                        </div>
                    </td>
                </tr>
            <?php
                    endwhile;
                endif;
                $stmt->close();
                $conn->close();
            ?>
        </tbody>
    </table>
</div>

<?php include_once "../includes/footer.php"; ?>

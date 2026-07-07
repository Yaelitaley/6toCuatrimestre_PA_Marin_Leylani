<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda de Contactos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-header">
        <h2><i class="fas fa-address-book"></i> AgendaPro</h2>
        <p>Gestión de Contactos</p>
    </div>
    <?php $ruta = $_SERVER['PHP_SELF']; ?>
    <nav class="sidebar-nav">
        <a href="../contactos/index.php" <?= strpos($ruta, 'contactos') !== false ? 'class="active"' : '' ?>>
            <i class="fas fa-users"></i> Contactos
        </a>
    </nav>
</aside>

<div class="main-content">

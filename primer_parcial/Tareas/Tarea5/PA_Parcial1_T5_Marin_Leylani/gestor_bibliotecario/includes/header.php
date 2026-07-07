<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor Bibliotecario</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-header">
        <h2><i class="fas fa-book-open"></i> Bibliotech</h2>
        <p>Sistema de Gestión</p>
    </div>
    <?php $ruta = $_SERVER['PHP_SELF']; ?>
    <nav class="sidebar-nav">
        <a href="../dashboard.php" <?= strpos($ruta, 'dashboard') !== false ? 'class="active"' : '' ?>><i class="fas fa-home"></i> Dashboard</a>
        <a href="../categorias/index.php" <?= strpos($ruta, 'categorias') !== false ? 'class="active"' : '' ?>><i class="fas fa-tags"></i> Categorías</a>
        <a href="../autores/index.php" <?= strpos($ruta, 'autores') !== false ? 'class="active"' : '' ?>><i class="fas fa-user"></i> Autores</a>
        <a href="../libros/index.php" <?= strpos($ruta, 'libros') !== false ? 'class="active"' : '' ?>><i class="fas fa-book"></i> Libros</a>
        <a href="../usuarios/index.php" <?= strpos($ruta, 'usuarios') !== false ? 'class="active"' : '' ?>><i class="fas fa-users"></i> Usuarios</a>
        <a href="../prestamos/index.php" <?= strpos($ruta, 'prestamos') !== false ? 'class="active"' : '' ?>><i class="fas fa-hand-holding-heart"></i> Préstamos</a>
        <a href="../contactos/index.php" <?= strpos($ruta, 'contactos') !== false ? 'class="active"' : '' ?>><i class="fas fa-address-book"></i> Contactos</a>
    </nav>
</aside>

<div class="main-content">

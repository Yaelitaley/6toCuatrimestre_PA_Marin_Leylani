<?php include 'BD.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>To Do List</title>
    <link rel="stylesheet" href="style.css"> 
    <style>
        .completada { text-decoration: line-through; color: gray; }
        .filtro-activo { font-weight: bold; }
        
        .main-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

    <div class="main-container">
        
        <header>
            <h1>Lista de Pendientes</h1>
        </header>

        <section class="input-group">
            <input type="text" id="nuevaTarea" placeholder="Escribe una tarea...">
            <button onclick="agregar()">Agregar</button>
        </section>

        <nav class="filters">
            <button onclick="filtrar('todas')" class="filtro-activo">Todas</button>
            <button onclick="filtrar('pendientes')">Pendientes</button>
            <button onclick="filtrar('completadas')">Completadas</button>
        </nav>

        <ul id="lista">
            <?php
            $sql = "SELECT * FROM tareas";
            $resultado = $conn->query($sql);
            while($t = $resultado->fetch_assoc()): ?>
                <li class="tarea-item <?php echo $t['completada'] ? 'completada' : ''; ?>" 
                    data-estado="<?php echo $t['completada'] ? '1' : '0'; ?>">
                    <span class="tarea-texto"><?php echo $t['descripcion']; ?></span>
                    <button onclick="this.parentElement.remove(); 
                    actualizarContador();">Eliminar</button>
                </li>
            <?php endwhile; ?>
        </ul>

        <footer>
            <p id="contador">0 tareas pendientes</p>
        </footer>

    </div>

    <script src="script.js"></script>
</body>
</html>
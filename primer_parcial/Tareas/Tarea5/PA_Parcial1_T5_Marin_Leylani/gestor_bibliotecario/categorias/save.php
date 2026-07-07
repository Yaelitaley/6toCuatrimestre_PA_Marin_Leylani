<?php
    require_once "../config/connection.php"; // Importo la conexión a la base de datos

    if ($_SERVER["REQUEST_METHOD"] !== "POST") { // Este if contiene una validación. Esta validación se asegura que nuestro script solo se ejecute si el método por el cual fue enviado el formulario es POST.
        header("Location: index.php");
        exit;
    }
    // Recibiendo las variables del formulario y sanitizándolas
    $nombre = trim($_POST["nombre"] ?? ""); // Recibimos el nombre de la categoría y lo sanitizamos usando la función trim() esta función elimina todos los espacios en blanco AL INICIO y AL FINAL de la cadena. ?? "" es un operador conocido como OPERADOR DE FUNCIÓN NULL, este operador te devolverá el valor de $_POST["nombre"] si existe, pero si no existe o es null, te devolverá un cadena vacía "", esto para evitar generar errores.
    $descripcion = trim($_POST["descripcion"] ?? ""); // Recibimos la descripción de la categoría y la sanitizamos de la misma manera que el nombre.

    if ($nombre === "") { // En caso de que el nombre esté vacío, redirigimos al formulario de creación con un mensaje de error.
        header("Location: create.php?error=" . urlencode("El nombre es obligatorio"));
        exit;
    }
    // Aquí va el código para crear la categoría usando una consulta preparada
    // ¿Qué es un prepared statement? Es una forma segura de ejecutar consultas SQL, haciendo uso de una plantilla de consulta con marcadores de posición, y luego vinculando los valores a esos marcadores. Esto ayuda a prevenir ataques de inyección SQL.
    $stmt = $conn->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)"); // $stmt es una variable que contiene nuestra consulta preparada. $conn->prepare() es un método que se utiliza para preparar una consulta SQL. Los signos de interrogación ? son los marcadores de posición, estos marcadores de posición serán reemplazados por los valores reales cuando ejecutemos la consulta.
    $stmt->bind_param("ss", $nombre, $descripcion); // Aquí le pasamos los valores de los marcadores de posición a nuestra consulta previamente preparada. "ss" indica el tipo de valor de los parámetros que voy a pasar a la consulta, para este ejemplo significa "string". $nombre y $descripcion son dos variables que contienen los valores que voy a insertar en mi base de datos. 

    if ($stmt->execute()){ // Aquí ejecutamos la consulta preparada, si la ejecución es exitosa, redirigimos al usuario a la página de listado de categorías con un mensaje de éxito
        header("Location: index.php?success=" . urlencode("Categoría creada exitosamente"));
        exit;
    } else{
        header("Location: create.php?error=".urlencode("Error al crear la categoría: " . $stmt->error));
        exit;
    }
    $stmt->close(); // Cerramos la consulta preparada
    $conn->close(); // Cerramos la conexión a la base de datos
?>

<?php
    require_once "../config/connection.php";

    if ($_SERVER["REQUEST_METHOD"] !== "POST") { // Verifico que la solicitud sea POST, si no lo es, redirijo al index
        header("Location: index.php");
        exit;
    }

    $titulo = trim($_POST["titulo"] ?? ""); // Obtenemos el título del libro, limpiamos espacios y si el campo llega "null", lo convertimos a cadena vacía
    $isbn = trim($_POST["isbn"] ?? ""); // Obtenemos el ISBN del libro, limpiamos espacios y si el campo llega "null", lo convertimos a cadena vacía
    $anio_publicacion = trim($_POST["anio_publicacion"] ?? "") ?: null; // Obtenemos el año de publicación del libro, limpiamos espacios y si el campo llega "null" o vacío, lo convertimos a null
    $editorial = trim($_POST["editorial"] ?? ""); // Obtenemos la editorial del libro, limpiamos espacios y si el campo llega "null", lo convertimos a cadena vacía
    $cantidad = intval($_POST["cantidad"] ?? 0); // Obtenemos la cantidad total de libros, si el campo llega vacío o "null", lo convertimos a 0
    $disponibles = intval($_POST["disponibles"] ?? 0); // Obtenemos la cantidad de libros disponibles, si el campo llega vacío o "null", lo convertimos a 0
    $id_categoria = intval($_POST["id_categoria"] ?? 0); // Obtenemos el ID de la categoría, si el campo llega vacío o "null", lo convertimos a 0
    $id_autores = $_POST["id_autores"] ?? []; // Obtenemos los IDs de los autores, si el campo llega vacío o "null", lo convertimos a un array vacío
    $id_libro = null; // Inicializamos la variable que almacenará el ID del libro insertado

    if ($titulo === "" || $id_categoria <= 0 || empty($id_autores)) {
        header("Location: create.php?error=" . urlencode("El título, la categoría y al menos un autor son obligatorios")); // Si el título es vacío, la categoría no es válida o no se seleccionó ningún autor, redirijo al formulario de creación con un mensaje de error
        exit;
    }

    // Aquí va el código para insertar el libro usando una consulta preparada
    $stmt = $conn->prepare("INSERT INTO libros (titulo, isbn, anio_publicacion, editorial, cantidad, disponibles, id_categoria) VALUES (?, ?, ?, ?, ?, ?, ?)"); // Preparo la consulta SQL para insertar un nuevo libro en la tabla libros
    $stmt->bind_param("ssisiii", $titulo, $isbn, $anio_publicacion, $editorial, $cantidad, $disponibles, $id_categoria); // Enlazo los parámetros de la consulta con las variables correspondientes
    if($stmt->execute()){
        $id_libro = $stmt->insert_id; // Obtenemos el ID del libro que acabamos de insertar para poder utilizarlo en la tabla libro autor
        header("Location: index.php?success=" . urlencode("Libro creado exitosamente"));
    }else{
        header("Location: create.php?error=" . urlencode("Error al crear el libro: " . $stmt->error));
    }
    $stmt->close(); // Cerramos la consulta preparada para liberar recursos
    
    // Aquí va el código para insertar en la tabla libro_autor usando una consulta preparada
    $stmt = $conn->prepare("INSERT INTO libro_autor (id_libro, id_autor) VALUES (?, ?)"); // Preparo la consulta SQL para insertar en la tabla libro_autor
    foreach($id_autores as $id_autor){
        $stmt->bind_param("ii", $id_libro, $id_autor);
        $stmt->execute();
    }
    $stmt->close(); // Cerramos la consulta preparada para liberar recursos
    $conn->close(); // Cerramos la conexión a la base de datos
?>

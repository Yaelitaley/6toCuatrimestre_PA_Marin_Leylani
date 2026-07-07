<?php
    require_once "../config/connection.php";

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Location: index.php");
        exit;
    }

    $id = intval($_POST["id"] ?? 0); 
    $nombre = trim($_POST["nombre"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");

    if ($id <= 0 || $nombre === "") {
        header("Location: index.php?error=" . urlencode("Datos inválidos"));
        exit;
    }

    // Aquí va el código para actualizar la categoría usando una consulta preparada
    $stmt = $conn->prepare("UPDATE categorias SET nombre = ?, descripcion = ? WHERE id = ?"); // Aquí hacemos una consulta preparada utilizando UPDATE para actualizar los datos de la categoría. Los signos "?" son marcadores de posición para los valores que se pasarán posteriormente.
    $stmt->bind_param("ssi", $nombre, $descripcion, $id); // Aquí le pasamos los valores de los marcadores de posición a nuestra consulta previamente preparada. "ssi".
    if($stmt->execute()){
        header("Location:index.php?success=" . urlencode("Categoría actualizada exitosamente"));
        exit;
    }else{
        header("Location: index.php?error=" .urlencode("Error al actualizar la categoría: " . $stmt->error));
        exit;
    }
    $stmt->close(); // Cerramos la consulta preparada
    $conn->close(); // Cerramos la conexión a la base de datos
?>

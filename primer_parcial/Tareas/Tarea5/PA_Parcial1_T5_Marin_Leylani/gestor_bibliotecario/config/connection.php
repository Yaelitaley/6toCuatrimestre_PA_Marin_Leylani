<?php
    require_once __DIR__ . "/config.php"; // Podemos importar archivos de php utilizando include o require. La diferencia es que include genera una advertencia si no se encuentra el archivo, sin embargo el script continúan ejecutándose. Por otro lado, require genera un error fatal si no se encuentra el archivo, es decir que la ejecución del script se detiene por completo.
    // Tenemos include_once y require_once, que en escencia funcionan prácicamente igual que include y require. La gran diferencia radica en que tanto include_once como require_once verifican si el archivo ya ha sido incluido previamente antes de volver a incluirlo, asegurando que solo hagamos la inclusión una vez.

    // Conexión a la base de datos utilizando mysqli
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name); // En una variable en este llamada "conn" estoy almacenando la conexión a la base de datos utilizando la clase mysql. Los parámetros que le paso al constructor son el host de la base de datos, el usuario, la contraseña y el nombre de la base de datos. 
    if ($conn->connect_error) { // Esto es una validación, que verifica que no haya existido un error en la conexión.
        die("Connection failed: " . $conn->connect_error); // Si ocurrió algún error, ya sea que algún dato está mal o que simplemente no se pudo conectar, con el método die() se detiene la ejecución del script y se muestra un mensaje de error que además incluye el error específico.
    }
    $conn->set_charset("utf8"); // Esto es solo la encodificación para mostrar de manera correcta los caracteres especiales.
?>
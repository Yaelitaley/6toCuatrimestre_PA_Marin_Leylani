<?php
require_once('../../config/connection.php'); // Importa la conexión a la base de datos
header("Access-Control-Allow-Origin: *"); // Permite que cualquier origen pueda acceder a esta API, sin importar su dominio. Esto nos va a ayudar a prevenir los famosos problemas de CORS (Cross-Origin Resource Sharing). ¿Qué es CORS? CORS es una política de seguridad implementada por los navegadores web para restringir las solicitudes HTTP que se puedan hacer a un dominio diferente al que sirvió la página web.
header("Content-Type: application/json; charset=UTF-8"); // Esta cabecer, indica el tipo de contenido que va a ser devuelto por esta API, para este ejemplo, va ser en formato JSON. Además, también especificamos la encodificación de caracteres, en este caso UTF-8.
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE"); // Esta cabecera especifica los métodos HTTP que esta API va a aceptar. Es una cabecera importante para temas de seguridad. ¿Qué ocurre si un cliente intenta hacer una solicitud no permitida? En este caso, el servidor responderá con un código de estado HTTP 405 Method Not Allowed, indicando que el método utilizado en la solicitud no está permitido para el recurso solicitado.
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"); // Esta cabecera especifica los encabezados HTTP que se permitirán en las solicitudes o peticiones recibidas por esta API. Es decir, si un cliente hace una solicitud a esta API, y envía una cabecer no permitida, el servidor responderá con un código de estado HTTP 400 Bad Request, indicando que la solicitud no se pudo procesar debido a encabezados no permitidos.

// Obtener el método HTTP de la solicitud
$method = $_SERVER['REQUEST_METHOD']; 



// Procesamos la solicitud según el método HTTP

//METODO GET 

switch ($method) { // Utilizamos la condicional switch-case para evaluar el valor de la variable $method. NOTA: Técnicamente igual podríamos utilizar una estructura if-else, sin embargo el switch-case es un método más limpio
    case 'GET':
        // solicitudes GET 
        if (isset($_GET['id'])) { // Con el método isset() verificamos que la variable $_GET['id'] esté definida es decir, que exista y que su valor no sea null
            $id = intval($_GET['id']); // Si la variable $_GET['id'] está definida, la almacenamos en una variable llamada $id. Nota: También podríamos trabajarla con la variable superglobal.
            $stmt = $conn->prepare("SELECT id, nombre, descripcion FROM categorias WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $resultado = $stmt->get_result();
            if ($resultado->num_rows > 0) {
                $categoria = $resultado->fetch_assoc();
                echo json_encode($categoria);
            } else {
                echo json_encode(array("mensaje" => "Categoría no encontrada"));
            }
            $stmt->close();
            $conn->close();
        } elseif (isset($_GET['nombre'])) {  
            $nombre = trim($_GET['nombre']);
            $stmt = $conn->prepare("SELECT id, nombre, descripcion FROM categorias WHERE nombre LIKE ?");
            $nombre_param = "%$nombre%"; 
            $stmt->bind_param("s", $nombre_param);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $categorias = array();
            if ($resultado->num_rows > 0) {
                while ($row = $resultado->fetch_assoc()) {
                    $categorias[] = $row;
                }
                echo json_encode($categorias);
            } else {
                echo json_encode(array("mensaje" => "Categoría no encontrada"));
            }
            $stmt->close();
            $conn->close();
        } else { 
            $stmt = $conn->prepare("SELECT id, nombre, descripcion FROM categorias");
            $stmt->execute();
            $resultado = $stmt->get_result();
            $categorias = array();
            while ($row = $resultado->fetch_assoc()) {
                $categorias[] = $row;
            }
            $respuesta = json_encode($categorias);
            echo $respuesta;
            $stmt->close();
            $conn->close();
        }
        break;


         // METODO POST 

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true); // Aquí lo que estoy haciendo es crear una variable llamada $data, y esta variable contiene la decodificación de los datos JSON, que se realiza mediante el método json_decode(). ¿A qué se refiere o para qué me sirve la decodificación? Recuerda que el cliente envía los datos en formato JSON, entonces para poder trabajarlos en nuestro código PHP necesitamos convertirlos de formato JSON a un formato que PHP pueda entender, en este caso usaremos un arreglo asociativo. El método json_decode() hace tal cual eso, convierte una cadena JSON en una estructura de datos de PHP. El parámetro file_get_contents("php://input") se utiliza para leer el cuerpo de la solicitud HTTP, y el parámetro true, indica que el resultado debe ser un arreglo asociativo.

        $titulo            = trim($data['titulo'] ?? "");
        $isbn              = trim($data['isbn'] ?? "");
        $anio_publicacion  = trim($data['anio_publicacion'] ?? "");
        $editorial         = trim($data['editorial'] ?? "");
        $cantidad          = trim($data['cantidad'] ?? "");
        $disponibles       = trim($data['disponibles'] ?? "");
        $id_categoria      = trim($data['id_categoria'] ?? "");

        if (
            !empty($titulo) &&
            !empty($isbn) &&
            !empty($anio_publicacion) &&
            !empty($editorial) &&
            !empty($cantidad) &&
            !empty($disponibles) &&
            !empty($id_categoria)
        ) {

            $stmt = $conn->prepare(
                "INSERT INTO libros (
                    titulo,
                    isbn,
                    anio_publicacion,
                    editorial,
                    cantidad,
                    disponibles,
                    id_categoria
                ) VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "sssssii",
                $titulo,
                $isbn,
                $anio_publicacion,
                $editorial,
                $cantidad,
                $disponibles,
                $id_categoria
            );

            if ($stmt->execute()) {
                http_response_code(201);
                echo json_encode([
                    "mensaje" => "Libro creado exitosamente"
                ]);
            } else {
                echo json_encode([
                    "mensaje" => "Error al crear el libro"
                ]);
            }

            $stmt->close();
            $conn->close();

        } else {

            echo json_encode([
                "mensaje" => "Todos los campos son obligatorios"
            ]);

        }

        break;

        //METODO PUT 
    case 'PUT':
         if (!isset($_GET['id'])){
            http_response_encode(400);
            echo json_encode(["mensaje" => "Debe enviar el id de libro a actualizar"]);
            break;
         }

         $id = intval($_GET['id']);
         $data = json_decode(file_get_contents("php://input"), true); 

        $titulo = trim($data['titulo'] ?? "");
        $isbn = trim($data['isb'] ?? "");
        $anio_publicacion = trim($data['anio_publicacion'] ?? "");
        $editorial = trim($data['ediotrial'] ?? "");
        $cantidad = trim($data['cantidad'] ?? "");
        $disponible = trim($data['disponible'] ?? "");
        $id_categoria = trim($data['id_catehoria'] ?? "");

        if (
            !empty($titulo) &&
            !empty($isbn) &&
            !empty($anio_publicacion) &&
            !empty($editorial) &&
            !empty($cantidad) &&
            !empty($disponible) &&
            !empty($id_categoria)
        );

        if($stmt->execute()){
            if($stmt -> affected_rows > 0){
                http_response_code(200);
                echo json_encode(["mensaje" => "Libro actualizado correctamente"]);
            }else{
                http_response_encode(404);
                echo json_encode(["mensaje" => "Libro no encontrado o sin cambios"]);
            }else{
                http_response_encode(500);
                echo json_encode(["menaje" => "Error al eliminar el libro"]);
            }
        }

         break

         //METODO PATCH
    case 'PATCH':
         if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode([
                "mensaje" => "Debe enviar el id del libro a actualizar"
            ]);
            break;
        }

        $id = intval($_GET['id']);
        $data = json_decode(file_get_contents("php://input"), true);

        $campos = array();
        $tipos = "";
        $valores = array();

        $columnas_permitidas = array(
            "titulo"           => "s",
            "isbn"             => "s",
            "anio_publicacion" => "s",
            "editorial"        => "s",
            "cantidad"         => "i",
            "disponibles"      => "i",
            "id_categoria"     => "i",
        );

        foreach ($columnas_permitidas as $columna => $tipo) {
            if (isset($data[$columna]) && trim($data[$columna]) !== "") {
                $campos[]  = "$columna = ?";
                $tipos    .= $tipo;
                $valores[] = trim($data[$columna]);
            }
        }

        if (count($campos) > 0) {

            $sql = "UPDATE libros SET " . implode(", ", $campos) . " WHERE id = ?";
            $tipos .= "i";
            $valores[] = $id;

            $stmt = $conn->prepare($sql);
            $stmt->bind_param($tipos, ...$valores); 

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    http_response_code(200);
                    echo json_encode([
                        "mensaje" => "Libro actualizado parcialmente con éxito"
                    ]);
                } else {
                    http_response_code(404);
                    echo json_encode([
                        "mensaje" => "Libro no encontrado o sin cambios"
                    ]);
                }
            } else {
                http_response_code(500);
                echo json_encode([
                    "mensaje" => "Error al actualizar el libro"
                ]);
            }

            $stmt->close();
            $conn->close();

        } else {

            http_response_code(400);
            echo json_encode([
                "mensaje" => "Debe enviar al menos un campo para actualizar"
            ]);

        }

        break;


        //METODO DELETE 
    case 'DELETE':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => Debe enviar el id del libro a eliminar]);
            break;
        }
        $id = intval($_GET['id']);

        $stmt = $conn->prepare("DELETE FROM libros WHERE id = ?");
        $stmt->bind_param("i",$id);

        if ($stmt->execute()){
            if ($stmt->affected_rows > 0){
                http_response_code(200);
                echo json_encode(["mensaje" => "Libro eliminado correctamente"]);
            }else{
                http_response_code(404);
                echo json_encode(["mensaje" => "Libro no encontrado"]);
            }
            $stmt->close();
            $conn->close();
        }
        break;
}

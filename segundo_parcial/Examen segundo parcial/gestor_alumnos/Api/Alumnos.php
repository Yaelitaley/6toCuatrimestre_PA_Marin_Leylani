<?php

require_once __DIR__ . "/../config/conexion.php";
require_once __DIR__ . "/../config/helpers.php";

setCommonHeaders();

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getConnection();

switch ($method){

 case 'GET':
    manejarget($pdo);
    break;

case 'POST';
 manejarPost($pdo);
 break;

case 'PUT';
manejarPut($pdo);
break;

case 'DELETE';
manejarDelete($pdo);
break;
    default:
        sendResponse(false, null, "Método no permitido.", 405);


}

function manejarget($pdo) {
    try{
        //GET - mostrar todos los alumnos 





        //GET - obtener x id 
        if (!empty($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT a.*, c.nombre AS carrera, cu.nombre AS cuatrimestre
                                   FROM alumnos a
                                   JOIN carreras c ON a.id_carrera = c.id
                                   JOIN cuatrimestres cu ON a.cuatrimestre_actual = cu.id
                                   WHERE a.id = ?");
            $stmt->execute([intval($_GET['id'])]);
            $alumno = $stmt->fetch();

            if (!$alumno) {
                sendResponse(false, null, "No se encontró el alumno solicitado.", 404);
            }
            sendResponse(true, $alumno, "Alumno encontrado.");
        }

        $sql = "SELECT a.*, c.nombre AS carrera, cu.nombre AS cuatrimestre
                FROM alumnos a
                JOIN carreras c ON a.id_carrera = c.id
                JOIN cuatrimestres cu ON a.cuatrimestre_actual = cu.id
                WHERE 1 = 1";
        $params = [];

        // GET - por apellido 

          if (!empty($_GET['apellido'])) {
            $sql .= " AND a.apellido LIKE ?";
            $params[] = "%" . $_GET['apellido'] . "%";
        }

        //GET - filtar x id y apellido  

    }
}

//=== POST / registrar alumno ===

function manejarPost($pdo) {
    $body = getJsonBody();

    $requeridos = ['nombre', 'apellido', 'email', 'telefono', 'fecha_nacimiento' 'activo'];
    $faltantes = requireFields($body, $requeridos);

    if (!empty($faltantes)) {
        sendResponse(false, null, "Faltan campos obligatorios: " . implode(", ", $faltantes), 400);
    }

    if (!filter_var($body['email'], FILTER_VALIDATE_EMAIL)) {
        sendResponse(false, null, "El email proporcionado no es válido.", 400);
    }

    $estatusValidos = ['1', '0'];
    $estatus = $body['estatus'] ?? '1';
    if (!in_array($estatus, $estatusValidos)) {
        sendResponse(false, null, "El estatus debe ser: 1= activo o 0= inactivo", 400);
    }

    try {
        $sql = "INSERT INTO alumnos
                (nombre, apellido, email, telefono,
                 fecha_nacimiento, activo)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $body['nombre'],
            $body['apellido'],
            $body['email'],
            $body['telefono'] ?? null,
            $body['fecha_nacimiento'] ?? null,
            $estatus
        ]);

        $nuevoId = $pdo->lastInsertId();
        sendResponse(true, ["id" => $nuevoId], "Alumno registrado correctamente.", 201);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            sendResponse(false, null, "La matrícula o el correo ya están registrados.", 409);
        }
        sendResponse(false, null, "Error al registrar alumno: " . $e->getMessage(), 500);
    }
}

// === PUT ===

function manejarPut($pdo) {
    if (empty($_GET['id'])) {
        sendResponse(false, null, "Debes indicar el ID del alumno a actualizar (?id=).", 400);
    }
    $id = intval($_GET['id']);
    $body = getJsonBody();

    $camposRequeridos = ['nombre', 'apellido', 'email', 'telefono', 'fecha_nacimiento', 'activo'];
    foreach ($camposRequeridos as $campos) {
        if (!isset($body[$campos])) {
            sendResponse(false, null, "El campo '$campos' es obligatorio.", 400);
        }
    }

    $nombre = trim($body['nombre']);
    $apellido = trim($body['apellido']);
    $email = trim($body['email']);
    $telefono = trim($body['telefono']);
    $fechaNacimiento = trim($body['fecha_nacimiento']);
    $activo = filter_var($body['activo'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? ($body['activo'] == 1 ? 1 : 0);

    // esta bien el email o no , como la validacion de arriba 
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendResponse(false, null, "El email no tiene un formato válido.", 400);
    }

    try {
      //verifica si el alumno (id) existe en la tabla alumnos 
        $check = $pdo->prepare("SELECT id FROM alumnos WHERE id = ?");
        $check->execute([$id]);
        if (!$check->fetch()) {
            sendResponse(false, null, "No se encontró el alumno con id $id.", 404);
        }

        //Actualiza datos 
        $sql = "UPDATE alumnos 
                SET nombre = ?, 
                    apellido = ?, 
                    email = ?, 
                    telefono = ?, 
                    fecha_nacimiento = ?, 
                    activo = ? 
                WHERE id = ?";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $apellido, $email, $telefono, $fechaNacimiento, $activo ? 1 : 0, $id]);

        // si todo bien , retorna 200 en exito 
        sendResponse(true, [
            "id" => $id,
            "nombre" => $nombre,
            "apellido" => $apellido,
            "email" => $email,
            "telefono" => $telefono,
            "fecha_nacimiento" => $fechaNacimiento,
            "activo" => (bool)$activo
        ], "Alumno actualizado con éxito.", 200);

    } catch (PDOException $e) {
        sendResponse(false, null, "Error al actualizar alumno: " . $e->getMessage(), 500);
    }
}

// === DELETE ===

function manejarDelete($pdo) {
    if (empty($_GET['id'])) {
        sendResponse(false, null, "Debes indicar el ID del alumno", 400);
    }
    $id = intval($_GET['id']);

    try {
        // si existe en la base 
        $check = $pdo->prepare("SELECT id FROM alumnos WHERE id = ?");
        $check->execute([$id]);
        if (!$check->fetch()) {
            sendResponse(false, null, "No se encontró el alumno con id $id.", 404);
        }

       // lo maneje como borrador logico , osea marcar como inactivo 
        $stmt = $pdo->prepare("UPDATE alumnos SET activo = 0 WHERE id = ?");
        $stmt->execute([$id]);

        //si se elimina bien , manda 204 
        sendResponse(true, null, "Alumno desactivado con éxito.", 204);

    } catch (PDOException $e) {
        sendResponse(false, null, "Error al eliminar alumno: " . $e->getMessage(), 500);
    }
}

// === POST - inscripciones ===

function inscribirAlumnoCurso($pdo) {
    $body = getJsonBody();
    //id_alumno , id_curso
    if (!isset($body['id_alumno']) || !isset($body['id_curso'])) {
        sendResponse(false, null, "Debes indicar 'id_alumno' e 'id_curso'.", 400);
    }

    $idAlumno = intval($body['id_alumno']);
    $idCurso = intval($body['id_curso']);

    try {
        //valida si existe o no en la base de datos el alumno
        $checkAlumno = $pdo->prepare("SELECT id FROM alumnos WHERE id = ?");
        $checkAlumno->execute([$idAlumno]);
        if (!$checkAlumno->fetch()) {
            sendResponse(false, null, "No se encontró el alumno con id $idAlumno.", 404);
        }

        // valida que exista o no en la base pero el curso 
        $checkCurso = $pdo->prepare("SELECT id FROM cursos WHERE id = ?");
        $checkCurso->execute([$idCurso]);
        if (!$checkCurso->fetch()) {
            sendResponse(false, null, "No se encontró el curso con id $idCurso.", 404);
        }

        //validacion de que no esta inscrito todavia
        $checkInscripcion = $pdo->prepare("SELECT id FROM inscripciones WHERE id_alumno = ? AND id_curso = ?");
        $checkInscripcion->execute([$idAlumno, $idCurso]);
        if ($checkInscripcion->fetch()) {
            sendResponse(false, null, "El alumno con id $idAlumno ya está inscrito en el curso con id $idCurso.", 409);
        }

        // registrar la inscripcion
        $sql = "INSERT INTO inscripciones (id_alumno, id_curso, fecha_inscripcion,) VALUES (?, ?, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idAlumno, $idCurso]);
        
        $idInscripcion = $pdo->lastInsertId();

        //si se inscribio bien , manda el 201
        sendResponse(true, [
            "id_inscripcion" => intval($idInscripcion),
            "id_alumno" => $idAlumno,
            "id_curso" => $idCurso
        ], "Inscripción realizada con éxito.", 201);

    } catch (PDOException $e) {
        sendResponse(false, null, "Error al inscribir" . $e->getMessage(), 500);
    }
}




// === GET- consultar cursos de un alumno ===
function obtenerCursosPorAlumno($pdo) {
    if (empty($_GET['id_alumno'])) {
        sendResponse(false, null, "Debes indicar el id del alumno (?id_alumno=).", 400);
    }
    $idAlumno = intval($_GET['id_alumno']);

    try {
        $checkAlumno = $pdo->prepare("SELECT id, nombre, apellido FROM alumnos WHERE id = ?");
        $checkAlumno->execute([$idAlumno]);
        $alumno = $checkAlumno->fetch(PDO::FETCH_ASSOC);

        if (!$alumno) {
            sendResponse(false, null, "No se encontró el alumno con id $idAlumno.", 404);
        }

    
        $sql = "SELECT 
                    i.id AS id_inscripcion,
                    i.fecha_inscripcion,
                    i.estado AS estado_inscripcion, -- o el campo de estatus en tu tabla inscripciones
                    c.id AS id_curso,
                    c.nombre AS nombre_curso,
                    c.descripcion,
                    c.creditos
                FROM inscripciones i
                INNER JOIN cursos c ON i.id_curso = c.id
                WHERE i.id_alumno = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idAlumno]);
        $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        sendResponse(true, [
            "alumno" => [
                "id" => intval($alumno['id']),
                "nombre_completo" => $alumno['nombre'] . ' ' . $alumno['apellido']
            ],
            "total_cursos" => count($cursos),
            "cursos" => $cursos
        ], "Cursos del alumno obtenidos con éxito.", 200);

    } catch (PDOException $e) {
        sendResponse(false, null, "Error al obtener los cursos del alumno: " . $e->getMessage(), 500);
    }
}
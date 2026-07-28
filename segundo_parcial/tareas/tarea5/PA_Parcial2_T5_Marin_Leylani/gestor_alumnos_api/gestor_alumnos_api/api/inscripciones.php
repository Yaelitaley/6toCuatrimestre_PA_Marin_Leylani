<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/helpers.php";

setCommonHeaders();

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getConnection();

switch ($method) {
    case 'GET':
        manejarGet($pdo);
        break;
    case 'POST':
        manejarPost($pdo);
        break;
    case 'DELETE':
        manejarDelete($pdo);
        break;
    default:
        sendResponse(false, null, "Método no permitido.", 405);
}

// ============================================================
// GET - Materias inscritas por un alumno / Alumnos inscritos en una materia
// ============================================================
function manejarGet($pdo) {
    try {
        // GET - Consultar las materias inscritas por un alumno
        if (!empty($_GET['id_alumno'])) {
            $idAlumno = intval($_GET['id_alumno']);

            $sql = "SELECT i.id, i.fecha_inscripcion, i.calificacion, i.periodo,
                           m.id AS id_materia, m.nombre AS materia, m.creditos,
                           c.nombre AS carrera, cu.nombre AS cuatrimestre
                    FROM inscripciones i
                    JOIN materias m ON i.id_materia = m.id
                    JOIN carreras c ON m.id_carrera = c.id
                    JOIN cuatrimestres cu ON m.id_cuatrimestre = cu.id
                    WHERE i.id_alumno = ?
                    ORDER BY i.fecha_inscripcion DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([$idAlumno]);
            $materias = $stmt->fetchAll();

            sendResponse(true, $materias, "Materias inscritas obtenidas correctamente.");
        }

        // GET - Consultar los alumnos inscritos en una materia
        if (!empty($_GET['id_materia'])) {
            $idMateria = intval($_GET['id_materia']);

            $sql = "SELECT i.id, i.fecha_inscripcion, i.calificacion, i.periodo,
                           a.id AS id_alumno, a.matricula, a.nombre, a.apellido_paterno, a.apellido_materno
                    FROM inscripciones i
                    JOIN alumnos a ON i.id_alumno = a.id
                    WHERE i.id_materia = ?
                    ORDER BY a.apellido_paterno ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([$idMateria]);
            $alumnos = $stmt->fetchAll();

            sendResponse(true, $alumnos, "Alumnos inscritos obtenidos correctamente.");
        }

        sendResponse(false, null, "Debes indicar 'id_alumno' o 'id_materia' como parámetro.", 400);
    } catch (PDOException $e) {
        sendResponse(false, null, "Error al consultar inscripciones: " . $e->getMessage(), 500);
    }
}

// ============================================================
// POST - Registrar una inscripción
// ============================================================
function manejarPost($pdo) {
    $body = getJsonBody();

    $requeridos = ['id_alumno', 'id_materia', 'fecha_inscripcion'];
    $faltantes = requireFields($body, $requeridos);

    if (!empty($faltantes)) {
        sendResponse(false, null, "Faltan campos obligatorios: " . implode(", ", $faltantes), 400);
    }

    try {
        // Validar que el alumno exista
        $stmtA = $pdo->prepare("SELECT id FROM alumnos WHERE id = ?");
        $stmtA->execute([intval($body['id_alumno'])]);
        if (!$stmtA->fetch()) {
            sendResponse(false, null, "El alumno indicado no existe.", 404);
        }

        // Validar que la materia exista
        $stmtM = $pdo->prepare("SELECT id FROM materias WHERE id = ?");
        $stmtM->execute([intval($body['id_materia'])]);
        if (!$stmtM->fetch()) {
            sendResponse(false, null, "La materia indicada no existe.", 404);
        }

        // Evitar inscripciones duplicadas
        $stmtDup = $pdo->prepare("SELECT id FROM inscripciones WHERE id_alumno = ? AND id_materia = ?");
        $stmtDup->execute([intval($body['id_alumno']), intval($body['id_materia'])]);
        if ($stmtDup->fetch()) {
            sendResponse(false, null, "El alumno ya está inscrito en esta materia.", 409);
        }

        $sql = "INSERT INTO inscripciones (id_alumno, id_materia, fecha_inscripcion, calificacion, periodo)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            intval($body['id_alumno']),
            intval($body['id_materia']),
            $body['fecha_inscripcion'],
            $body['calificacion'] ?? null,
            $body['periodo'] ?? null
        ]);

        $nuevoId = $pdo->lastInsertId();
        sendResponse(true, ["id" => $nuevoId], "Inscripción registrada correctamente.", 201);
    } catch (PDOException $e) {
        sendResponse(false, null, "Error al registrar inscripción: " . $e->getMessage(), 500);
    }
}

// ============================================================
// DELETE - Eliminar una inscripción
// ============================================================
function manejarDelete($pdo) {
    if (empty($_GET['id'])) {
        sendResponse(false, null, "Debes indicar el id de la inscripción a eliminar (?id=).", 400);
    }
    $id = intval($_GET['id']);

    try {
        $check = $pdo->prepare("SELECT id FROM inscripciones WHERE id = ?");
        $check->execute([$id]);
        if (!$check->fetch()) {
            sendResponse(false, null, "No se encontró la inscripción con id $id.", 404);
        }

        $stmt = $pdo->prepare("DELETE FROM inscripciones WHERE id = ?");
        $stmt->execute([$id]);

        sendResponse(true, null, "Inscripción eliminada correctamente.");
    } catch (PDOException $e) {
        sendResponse(false, null, "Error al eliminar inscripción: " . $e->getMessage(), 500);
    }
}

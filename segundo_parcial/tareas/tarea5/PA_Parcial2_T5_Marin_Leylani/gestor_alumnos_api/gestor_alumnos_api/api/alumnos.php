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
    case 'PUT':
        manejarPut($pdo);
        break;
    case 'PATCH':
        manejarPatch($pdo);
        break;
    case 'DELETE':
        manejarDelete($pdo);
        break;
    default:
        sendResponse(false, null, "Método no permitido.", 405);
}

function manejarGet($pdo) {
    try {
        // GET - Obtener un alumno por ID (usado al editar)
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

        // GET - Buscar por matrícula
        if (!empty($_GET['matricula'])) {
            $sql .= " AND a.matricula LIKE ?";
            $params[] = "%" . $_GET['matricula'] . "%";
        }

        // GET - Buscar por nombre 
        if (!empty($_GET['nombre'])) {
            $sql .= " AND (a.nombre LIKE ? OR a.apellido_paterno LIKE ? OR a.apellido_materno LIKE ?)";
            $busqueda = "%" . $_GET['nombre'] . "%";
            $params[] = $busqueda;
            $params[] = $busqueda;
            $params[] = $busqueda;
        }

        // GET - X carrera
        if (!empty($_GET['id_carrera'])) {
            $sql .= " AND a.id_carrera = ?";
            $params[] = intval($_GET['id_carrera']);
        }

        $sql .= " ORDER BY a.apellido_paterno ASC, a.nombre ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $alumnos = $stmt->fetchAll();

        sendResponse(true, $alumnos, "Alumnos obtenidos correctamente.");
    } catch (PDOException $e) {
        sendResponse(false, null, "Error al consultar alumnos: " . $e->getMessage(), 500);
    }
}

// ============================================================
// POST - Registrar un alumno
// ============================================================
function manejarPost($pdo) {
    $body = getJsonBody();

    $requeridos = ['matricula', 'nombre', 'apellido_paterno', 'correo', 'id_carrera', 'cuatrimestre_actual'];
    $faltantes = requireFields($body, $requeridos);

    if (!empty($faltantes)) {
        sendResponse(false, null, "Faltan campos obligatorios: " . implode(", ", $faltantes), 400);
    }

    if (!filter_var($body['correo'], FILTER_VALIDATE_EMAIL)) {
        sendResponse(false, null, "El correo proporcionado no es válido.", 400);
    }

    $estatusValidos = ['Activo', 'Baja', 'Egresado'];
    $estatus = $body['estatus'] ?? 'Activo';
    if (!in_array($estatus, $estatusValidos)) {
        sendResponse(false, null, "El estatus debe ser: Activo, Baja o Egresado.", 400);
    }

    try {
        $sql = "INSERT INTO alumnos
                (matricula, nombre, apellido_paterno, apellido_materno, correo, telefono,
                 fecha_nacimiento, id_carrera, cuatrimestre_actual, estatus)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $body['matricula'],
            $body['nombre'],
            $body['apellido_paterno'],
            $body['apellido_materno'] ?? null,
            $body['correo'],
            $body['telefono'] ?? null,
            $body['fecha_nacimiento'] ?? null,
            intval($body['id_carrera']),
            intval($body['cuatrimestre_actual']),
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

// ============================================================
// PUT - Actualizar completamente un alumno
// ============================================================
function manejarPut($pdo) {
    if (empty($_GET['id'])) {
        sendResponse(false, null, "Debes indicar el id del alumno a actualizar (?id=).", 400);
    }
    $id = intval($_GET['id']);
    $body = getJsonBody();

    $requeridos = ['matricula', 'nombre', 'apellido_paterno', 'correo', 'id_carrera', 'cuatrimestre_actual', 'estatus'];
    $faltantes = requireFields($body, $requeridos);

    if (!empty($faltantes)) {
        sendResponse(false, null, "Faltan campos obligatorios: " . implode(", ", $faltantes), 400);
    }

    if (!filter_var($body['correo'], FILTER_VALIDATE_EMAIL)) {
        sendResponse(false, null, "El correo proporcionado no es válido.", 400);
    }

    $estatusValidos = ['Activo', 'Baja', 'Egresado'];
    if (!in_array($body['estatus'], $estatusValidos)) {
        sendResponse(false, null, "El estatus debe ser: Activo, Baja o Egresado.", 400);
    }

    try {
        $check = $pdo->prepare("SELECT id FROM alumnos WHERE id = ?");
        $check->execute([$id]);
        if (!$check->fetch()) {
            sendResponse(false, null, "No se encontró el alumno con id $id.", 404);
        }

        $sql = "UPDATE alumnos SET
                    matricula = ?, nombre = ?, apellido_paterno = ?, apellido_materno = ?,
                    correo = ?, telefono = ?, fecha_nacimiento = ?, id_carrera = ?,
                    cuatrimestre_actual = ?, estatus = ?
                WHERE id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $body['matricula'],
            $body['nombre'],
            $body['apellido_paterno'],
            $body['apellido_materno'] ?? null,
            $body['correo'],
            $body['telefono'] ?? null,
            $body['fecha_nacimiento'] ?? null,
            intval($body['id_carrera']),
            intval($body['cuatrimestre_actual']),
            $body['estatus'],
            $id
        ]);

        sendResponse(true, null, "Alumno actualizado correctamente.");
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            sendResponse(false, null, "La matrícula o el correo ya están registrados en otro alumno.", 409);
        }
        sendResponse(false, null, "Error al actualizar alumno: " . $e->getMessage(), 500);
    }
}

// ============================================================
// PATCH - Actualizar únicamente el estatus o el cuatrimestre actual
// ============================================================
function manejarPatch($pdo) {
    if (empty($_GET['id'])) {
        sendResponse(false, null, "Debes indicar el id del alumno a actualizar (?id=).", 400);
    }
    $id = intval($_GET['id']);
    $body = getJsonBody();

    if (!isset($body['estatus']) && !isset($body['cuatrimestre_actual'])) {
        sendResponse(false, null, "Debes enviar 'estatus' y/o 'cuatrimestre_actual' para actualizar.", 400);
    }

    $campos = [];
    $params = [];

    if (isset($body['estatus'])) {
        $estatusValidos = ['Activo', 'Baja', 'Egresado'];
        if (!in_array($body['estatus'], $estatusValidos)) {
            sendResponse(false, null, "El estatus debe ser: Activo, Baja o Egresado.", 400);
        }
        $campos[] = "estatus = ?";
        $params[] = $body['estatus'];
    }

    if (isset($body['cuatrimestre_actual'])) {
        $campos[] = "cuatrimestre_actual = ?";
        $params[] = intval($body['cuatrimestre_actual']);
    }

    $params[] = $id;

    try {
        $check = $pdo->prepare("SELECT id FROM alumnos WHERE id = ?");
        $check->execute([$id]);
        if (!$check->fetch()) {
            sendResponse(false, null, "No se encontró el alumno con id $id.", 404);
        }

        $sql = "UPDATE alumnos SET " . implode(", ", $campos) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        sendResponse(true, null, "Alumno actualizado parcialmente con éxito.");
    } catch (PDOException $e) {
        sendResponse(false, null, "Error al actualizar alumno: " . $e->getMessage(), 500);
    }
}

// ============================================================
// DELETE - Eliminar un alumno
// ============================================================
function manejarDelete($pdo) {
    if (empty($_GET['id'])) {
        sendResponse(false, null, "Debes indicar el id del alumno a eliminar (?id=).", 400);
    }
    $id = intval($_GET['id']);

    try {
        $check = $pdo->prepare("SELECT id FROM alumnos WHERE id = ?");
        $check->execute([$id]);
        if (!$check->fetch()) {
            sendResponse(false, null, "No se encontró el alumno con id $id.", 404);
        }

        // Eliminar primero sus inscripciones para respetar la integridad referencial
        $pdo->prepare("DELETE FROM inscripciones WHERE id_alumno = ?")->execute([$id]);

        $stmt = $pdo->prepare("DELETE FROM alumnos WHERE id = ?");
        $stmt->execute([$id]);

        sendResponse(true, null, "Alumno eliminado correctamente.");
    } catch (PDOException $e) {
        sendResponse(false, null, "Error al eliminar alumno: " . $e->getMessage(), 500);
    }
}

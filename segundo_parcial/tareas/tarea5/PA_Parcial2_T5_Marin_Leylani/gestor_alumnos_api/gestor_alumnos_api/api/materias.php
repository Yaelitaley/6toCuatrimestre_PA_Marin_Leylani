<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/helpers.php";

setCommonHeaders();

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'GET') {
    sendResponse(false, null, "Método no permitido. Esta API solo acepta GET.", 405);
}

$pdo = getConnection();

try {
    $sql = "SELECT m.*, c.nombre AS carrera, cu.nombre AS cuatrimestre
            FROM materias m
            JOIN carreras c ON m.id_carrera = c.id
            JOIN cuatrimestres cu ON m.id_cuatrimestre = cu.id
            WHERE 1 = 1";
    $params = [];

    // GET - Buscar por nombre
    if (!empty($_GET['nombre'])) {
        $sql .= " AND m.nombre LIKE ?";
        $params[] = "%" . $_GET['nombre'] . "%";
    }

    // GET - FX carrera
    if (!empty($_GET['id_carrera'])) {
        $sql .= " AND m.id_carrera = ?";
        $params[] = intval($_GET['id_carrera']);
    }

    // GET - X cuatrimestre
    if (!empty($_GET['id_cuatrimestre'])) {
        $sql .= " AND m.id_cuatrimestre = ?";
        $params[] = intval($_GET['id_cuatrimestre']);
    }

    $sql .= " ORDER BY m.id_cuatrimestre ASC, m.nombre ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $materias = $stmt->fetchAll();

    sendResponse(true, $materias, "Materias obtenidas correctamente.");
} catch (PDOException $e) {
    sendResponse(false, null, "Error al consultar materias: " . $e->getMessage(), 500);
}

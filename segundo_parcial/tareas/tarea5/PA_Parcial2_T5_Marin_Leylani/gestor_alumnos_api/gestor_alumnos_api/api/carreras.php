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
    if (isset($_GET['id'])) {
        // GET - Obtener una carrera por ID
        $id = intval($_GET['id']);

        $stmt = $pdo->prepare("SELECT * FROM carreras WHERE id = ?");
        $stmt->execute([$id]);
        $carrera = $stmt->fetch();

        if (!$carrera) {
            sendResponse(false, null, "No se encontró la carrera con id $id.", 404);
        }

        sendResponse(true, $carrera, "Carrera encontrada.");
    } else {
        // GET - Obtener todas las carreras
        $stmt = $pdo->query("SELECT * FROM carreras ORDER BY nombre ASC");
        $carreras = $stmt->fetchAll();

        sendResponse(true, $carreras, "Carreras obtenidas correctamente.");
    }
} catch (PDOException $e) {
    sendResponse(false, null, "Error al consultar carreras: " . $e->getMessage(), 500);
}

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
    // GET - Obtener todos los cuatrimestres
    $stmt = $pdo->query("SELECT * FROM cuatrimestres ORDER BY id ASC");
    $cuatrimestres = $stmt->fetchAll();

    sendResponse(true, $cuatrimestres, "Cuatrimestres obtenidos correctamente.");
} catch (PDOException $e) {
    sendResponse(false, null, "Error al consultar cuatrimestres: " . $e->getMessage(), 500);
}

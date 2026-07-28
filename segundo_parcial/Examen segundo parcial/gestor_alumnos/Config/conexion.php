<?php
function getConnection() {
    $host = "localhost";
    $db   = "gestor_alumnos";
    $user = "root";
    $pass = "";
    $charset = "utf8mb4";

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        http_response_code(500);
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode([
            "success" => false,
            "message" => "Error de conexión a la base de datos: " . $e->getMessage()
        ]);
        exit;
    }
}
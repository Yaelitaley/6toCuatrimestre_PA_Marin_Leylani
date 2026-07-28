<?php

function setCommonHeaders() {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Content-Type: application/json; charset=utf-8");

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

function sendResponse($success, $data = null, $message = "", $code = 200) {
    http_response_code($code);
    $response = ["success" => $success];
    if ($message !== "") {
        $response["message"] = $message;
    }
    if ($data !== null) {
        $response["data"] = $data;
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

function getJsonBody() {
    $input = file_get_contents("php://input");
    $decoded = json_decode($input, true);
    return is_array($decoded) ? $decoded : [];
}

function requireFields($body, $fields) {
    $missing = [];
    foreach ($fields as $field) {
        if (!isset($body[$field]) || $body[$field] === "") {
            $missing[] = $field;
        }
    }
    return $missing;
}

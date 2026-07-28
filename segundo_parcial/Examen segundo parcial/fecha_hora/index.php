<?php
header('Content-Type: application/json; charset=utf-8');

$respuesta = [
    'fecha' => date('Y-m-d H:i:s'),
    'mensaje' => 'Hora actual obtenida con éxito'
];

echo json_encode($respuesta);
?>
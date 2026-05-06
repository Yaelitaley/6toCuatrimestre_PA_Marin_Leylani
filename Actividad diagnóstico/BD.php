<?php
$conn = new mysqli("localhost", "root", "", "evaluacion_diagnostico");
if ($conn->connect_error) die("Error: " . $conn->connect_error);
?>
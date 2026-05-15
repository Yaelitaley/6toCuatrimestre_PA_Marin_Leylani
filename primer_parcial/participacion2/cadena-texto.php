<?php

function repetirTexto($texto, $n) {
    if ($n <= 0) {
        return "";
    }
return $texto . " " . repetirTexto($texto, $n - 1);
}
$mensaje = "Leylani";
$repeticiones = 10;

echo "--- Ejercicio 3 ---<br>";
echo "Texto original: $mensaje<br>";
echo "Repetido $repeticiones veces: <br>";
echo repetirTexto($mensaje, $repeticiones);

?>
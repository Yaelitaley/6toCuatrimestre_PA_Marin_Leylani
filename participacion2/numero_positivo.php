<?php
function contarPositivos($arreglo, $indice = 0) {
    
    if ($indice == count($arreglo)) {
        return 0;
    }
    $esPositivo = ($arreglo[$indice] > 0) ? 1 : 0;
    return $esPositivo + contarPositivos($arreglo, $indice + 1);
}

$misNumeros = [5, -2, 10, 7, 8, -1, 3]; 

echo "--- Ejercicio numeros positivos ---<br>";
echo "Arreglo: [" . implode(", ", $misNumeros) . "]<br>";
echo "Cantidad de números positivos: " . contarPositivos($misNumeros);

?>
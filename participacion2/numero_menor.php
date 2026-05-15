<?php
function encontrarMenor($arreglo, $indice = 0) {
    
    if ($indice == count($arreglo) - 1) {
        return $arreglo[$indice];
    }

    $menorDelResto = encontrarMenor($arreglo, $indice + 1);
    if ($arreglo[$indice] < $menorDelResto) {
        return $arreglo[$indice]; 
    } else {
        return $menorDelResto;   
    }
}

$valores = [15, 8, 22, 3, 10, 5]; 

echo "--- Ejercicio numero menor ---<br>";
echo "Arreglo: [" . implode(", ", $valores) . "]<br>";
echo "El número menor es: " . encontrarMenor($valores);

?>
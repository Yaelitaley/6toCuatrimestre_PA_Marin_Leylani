
<?php
function contarNumeros($buscado, $arreglo, $indice = 0) {
    
    if ($indice == count($arreglo)) {
        return 0;
    }
    $coincidencia = ($arreglo[$indice] == $buscado) ? 1 : 0;
    return $coincidencia + contarNumeros($buscado, $arreglo, $indice + 1);
}
$datos = [1, 2, 2, 2, 4, 2, 5, 2];
$numeroABuscar = 2;

echo "El numero $numeroABuscar aparece: " . contarNumeros
($numeroABuscar, $datos) . " veces.";

?>
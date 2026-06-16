<?php
/* Escribe una funcion recursiva que devuelva cuantos digitos tiene un numero ,
  por ejemplo contarDigitos (38345) devuelva 5 */
  function contarDigitos($n) {
    $n = abs($n); //esto es para ver si el numero a contar es positivo 
    
    if ($n < 10) { // si es solo uno , regresa 1
        return 1;
    }
    return 1 + contarDigitos(floor($n / 10)); //"contarDigitos es mi funcion 
}

echo contarDigitos(38345); //ejemplo que dio en el examen , para que no haya pierde 
?>
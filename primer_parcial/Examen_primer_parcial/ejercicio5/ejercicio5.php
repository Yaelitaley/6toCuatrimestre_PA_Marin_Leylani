/*haz un programa que contenga la funcion recursiva sumaDigitos($n) que reciba un numero entero y devuelva la suma de todos sus digitos . Ejemplo sumaDigitos(1234) debe sumar 
1+2+3+4 */

<?php


function sumaDigitos($n) {
    $n = abs($n);
    if ($n == 0) {
        return 0; //si el numero es 0 , regresa 0 
    }
    
    return ($n % 10) + sumaDigitos((int)($n / 10));
}

$numero = 1234;
$resultado = sumaDigitos($numero);

echo "La suma de los dígitos de $numero es: $resultado\n"; 
?>
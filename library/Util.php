<?php

class Util
{
    public static $ENTRADAS_SAIDAS = array(1 => 0.67, 2 => 2, 3 => 4, 4 => 6.67, 5 => 10, 6 => 14, 7 => 18.67, 8 => 24, 9 => 30, 10 => 36.67);
    public static $OPERADORES = array('+', '-', '*', '/');

    public static function getOperadorOuConstanteAleatorio() {
        $conjunto = array(self::getOperadorAleatorio(), self::getConstanteAleatorio());
        return $conjunto[array_rand($conjunto, 1)];
    }

    public static function getOperadorAleatorio() {
        $conjunto = self::$OPERADORES;
        return $conjunto[array_rand($conjunto)];
    }

    public static function getConstanteAleatorio() {
        $conjunto = self::gerarNumeroAleatorio(3);
        $conjunto[] = 'x';
        return $conjunto[array_rand($conjunto)];
    }

    public static function gerarNumeroAleatorio($quantidade) {
        $numerosAleatorios = array();
        do {
            $numero = rand(1, 10);
            $numero = rand(0, 1) ? $numero : $numero * (-1);
            if (!in_array($numero, $numerosAleatorios)) {
                $numerosAleatorios[] = $numero;
            }
        } while (count($numerosAleatorios) < $quantidade);
        return $numerosAleatorios;
    }

    public static function isOperador($valor) {
        return in_array($valor, self::$OPERADORES);
    }

    public static function isConstante($valor) {
        return !self::isOperador($valor);
    }
}
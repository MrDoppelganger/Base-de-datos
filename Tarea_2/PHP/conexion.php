<?php

$host = "localhost";
$usuario = "Gescon";
$contrasena = "qazwsxedc123"; 
$base_de_datos = "GesconDatabase";

$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error); // Línea 2 es esta
}

$conexion->set_charset("utf8");

?>
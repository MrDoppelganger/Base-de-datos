<?php
session_start();
require_once "conexion.php";

// Verificación básica
if (!isset($_SESSION["usuario_rut"]) || $_SESSION["usuario_tipo"] !== "autor") {
    header("Location: login.php");
    exit();
}

$rut_autor = $_SESSION["usuario_rut"];

// Eliminar el autor directamente
$conexion->query("DELETE FROM Autores WHERE rut_autor = '$rut_autor'");

// Destruir sesión y redirigir
session_unset();
session_destroy();
header("Location: login.php");
exit();
?>
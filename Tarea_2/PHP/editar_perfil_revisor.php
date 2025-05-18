<?php
//-------------------- INICIALIZACIÓN -----------------
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciamos sesión y nos conectamos a la base de datos
session_start();
require_once "conexion.php";

//-------------------- VERIFICACIÓN DE SESIÓN -----------------
if (!isset($_SESSION["usuario_rut"]) || $_SESSION["usuario_tipo"] !== "revisor") {
    header("Location: login.php");
    exit();
}

// Guardamos datos iniciales
$rut_revisor = $_SESSION["usuario_rut"];
$error = $mensaje = "";

//-------------------- OBTENER DATOS DEL USUARIO -----------------
$result = $conexion->query("SELECT * FROM Revisores WHERE rut_revisor = '$rut_revisor'");
$revisor = $result->fetch_assoc();

//-------------------- PROCESAMIENTO DEL FORMULARIO -----------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtenemos los datos del formulario
    $nombre = trim($_POST["nombre"]);
    $correo = trim($_POST["correo"]);
    $usuario = trim($_POST["usuario"]);
    $nueva_contrasena = $_POST["nueva_contrasena"] ?? '';

    //------------------ VALIDACIONES BÁSICAS ----------------------
    if (empty($nombre) || empty($correo) || empty($usuario)) {
        $error = "Nombre, correo y usuario son campos obligatorios.";
    }

    // Validación de formato de email
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "El correo electrónico no es válido.";
    }

    //-------------------- ACTUALIZACIÓN EN BASE DE DATOS -----------------
    if (empty($error)) {
        // Construimos la consulta de actualización
        $query = "UPDATE Revisores SET 
                 nombre_revisor = '$nombre', 
                 correo_revisor = '$correo', 
                 usuario_revisor = '$usuario'";

        // Si hay una nueva contraseña, la incluimos en la actualización
        if (!empty($nueva_contrasena)) {
            $contrasena_hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
            $query .= ", contraseña_revisor = '$contrasena_hash'";
        }

        $query .= " WHERE rut_revisor = '$rut_revisor'";

        //-------------------- EJECUTAMOS LA ACTUALIZACIÓN -----------------
        if ($conexion->query($query)) {
            $mensaje = "Perfil actualizado correctamente.";
            $_SESSION["usuario_nombre"] = $nombre;

            // Actualizamos los datos en la sesión
            $revisor['nombre_revisor'] = $nombre;
            $revisor['correo_revisor'] = $correo;
            $revisor['usuario_revisor'] = $usuario;
        } else {
            $error = "Error al actualizar el perfil.";
        }
    }
}

//-------------------- REDIRECCIÓN Y ENVÍO DE MENSAJES -----------------
$_SESSION["revisor_data"] = $revisor; // Guardamos datos para mostrar en el formulario
$_SESSION["error"] = $error;
$_SESSION["mensaje"] = $mensaje;
header("Location: ../HTML/editar_perfil_revisor_view.php");
exit();

?>

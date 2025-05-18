<?php
//-------------------- INICIALIZACIÓN -----------------
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciamos sesión y nos conectamos a la base de datos
session_start();
require_once "conexion.php";

//-------------------- VERIFICACIÓN DE SESIÓN -----------------
if (!isset($_SESSION["usuario_rut"]) || $_SESSION["usuario_tipo"] !== "autor") {
    header("Location: login.php");
    exit();
}

// Guardamos datos iniciales
$rut_autor = $_SESSION["usuario_rut"];
$error = $mensaje = "";

//-------------------- OBTENER DATOS DEL USUARIO -----------------
$result = $conexion->query("SELECT * FROM Autores WHERE rut_autor = '$rut_autor'");
$autor = $result->fetch_assoc();

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
        $query = "UPDATE Autores SET 
                 nombre_autor = '$nombre', 
                 correo_autor = '$correo', 
                 usuario_autor = '$usuario'";

        // Si hay una nueva contraseña, la incluimos en la actualización
        if (!empty($nueva_contrasena)) {
            $contrasena_hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
            $query .= ", contraseña_autor = '$contrasena_hash'";
        }

        $query .= " WHERE rut_autor = '$rut_autor'";

        //-------------------- EJECUTAMOS LA ACTUALIZACIÓN -----------------
        if ($conexion->query($query)) {
            $mensaje = "Perfil actualizado correctamente.";
            $_SESSION["usuario_nombre"] = $nombre;

            // Actualizamos los datos en la sesión
            $autor['nombre_autor'] = $nombre;
            $autor['correo_autor'] = $correo;
            $autor['usuario_autor'] = $usuario;
        } else {
            $error = "Error al actualizar el perfil.";
        }
    }
}

//-------------------- REDIRECCIÓN Y ENVÍO DE MENSAJES -----------------
$_SESSION["autor_data"] = $autor; // Guardamos datos para mostrar en el formulario
$_SESSION["error"] = $error;
$_SESSION["mensaje"] = $mensaje;
header("Location: ../HTML/editar_perfil_autor_view.php");
exit();

?>

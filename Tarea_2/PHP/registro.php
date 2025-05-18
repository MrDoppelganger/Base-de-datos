<?php
//-------------------- INICIALIZACIÓN -----------------
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciamos sesión y nos conectamos a la base de datos
session_start();
include("conexion.php");

// Array para almacenar mensajes de error
$errores = array();

//------------------- PROCESAMIENTO DEL FORMULARIO -----------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtenemos y limpiamos los datos del formulario
    $rut = trim($_POST["rut"]);
    $nombre = trim($_POST["nombre"]);
    $correo = trim($_POST["correo"]);
    $tipo_usuario = $_POST["tipo_usuario"]; // 'autor' o 'revisor'
    $usuario = trim($_POST["usuario"]);
    $password = $_POST["password"];

    //------------------ VALIDACIONES BÁSICAS ----------------------
    // Validación de campos obligatorios
    if (empty($rut) || empty($nombre) || empty($correo) || empty($tipo_usuario) || empty($usuario) || empty($password)) {
        $errores[] = "Todos los campos son obligatorios.";
    }

    // Validación de formato de email
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electrónico no es válido.";
    }

    // Validación de longitud mínima de contraseña
    if (strlen($password) < 8) {
        $errores[] = "La contraseña debe tener al menos 8 caracteres.";
    }

    //------------------ VERIFICACIÓN DE USUARIO EXISTENTE ----------------------
    if (empty($errores)) {
        // Determinamos la tabla y campos según el tipo de usuario
        $tabla = ($tipo_usuario == "autor") ? "Autores" : "Revisores";
        $campo_usuario = ($tipo_usuario == "autor") ? "usuario_autor" : "usuario_revisor";
        $campo_rut = ($tipo_usuario == "autor") ? "rut_autor" : "rut_revisor";

        // Verificamos si el usuario ya existe (Consulta preparada)
        $query = "SELECT COUNT(*) FROM $tabla WHERE $campo_usuario = ? OR $campo_rut = ?";
        $stmt = mysqli_prepare($conexion, $query);
        mysqli_stmt_bind_param($stmt, "ss", $usuario, $rut);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $count);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($count > 0) {
            $errores[] = "El usuario o RUT ya están registrados.";
        }
    }

    //------------------ REGISTRO EN LA BASE DE DATOS ----------------------
    if (empty($errores)) {
        // Hasheamos la contraseña
        $password_hasheado = password_hash($password, PASSWORD_DEFAULT);

        // Preparamos la consulta según el tipo de usuario (Esto nos proteje contra la inyeccion de SQL)
        if ($tipo_usuario == "autor") {
            $sql = "INSERT INTO Autores (rut_autor, nombre_autor, correo_autor, rol_autor, usuario_autor, contraseña_autor) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $rol = "autor";
        } else {
            $sql = "INSERT INTO Revisores (rut_revisor, nombre_revisor, correo_revisor, rol_revisor, usuario_revisor, contraseña_revisor) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $rol = "revisor";
        }

        // Ejecutamos la consulta preparada
        $stmt = mysqli_prepare($conexion, $sql);
        if ($stmt) {
            //Asignamos los atributos
            mysqli_stmt_bind_param($stmt, "ssssss", $rut, $nombre, $correo, $rol, $usuario, $password_hasheado);

            if (mysqli_stmt_execute($stmt)) {
                // Registro exitoso - redirigimos a página de éxito
                $_SESSION['registro_exitoso'] = true;
                header("Location: registro_exitoso.php");
                exit();
            } else {
                $errores[] = "Error al registrar el usuario. Por favor, inténtalo nuevamente.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $errores[] = "Error en el sistema. Por favor, inténtalo más tarde.";
        }
    }
}

//------------------ MOSTRAR FORMULARIO CON ERRORES ----------------------
// Incluimos el HTML del formulario de registro
include("../HTML/registro.html");

// Mostramos errores si existen
if (!empty($errores)) {
    echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                const errorContainer = document.createElement("div");
                errorContainer.className = "error-message";
                errorContainer.innerHTML = `'.implode("<br>", array_map('htmlspecialchars', $errores)).'`;
                
                const form = document.querySelector("form");
                if (form) {
                    form.insertBefore(errorContainer, form.firstChild);
                }
            });
          </script>';
}
?>
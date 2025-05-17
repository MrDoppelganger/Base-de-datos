<?php
# Con esto te sale el error de consola
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); 
include("conexion.php");

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = mysqli_real_escape_string($conexion, $_POST["usuario"]);
    $password = $_POST["password"];

    if (empty($usuario) || empty($password)) {
        $error_message = "Por favor, ingresa usuario y contraseña.";
    } else {
        // Buscar en tabla autores
        $query_autor = "SELECT rut_autor AS rut, nombre_autor AS nombre, correo_autor AS correo, usuario_autor AS usuario, contraseña_autor AS password 
                        FROM Autores 
                        WHERE usuario_autor = '$usuario'";
        $result_autor = mysqli_query($conexion, $query_autor);

        if ($result_autor && mysqli_num_rows($result_autor) == 1) {
            $row = mysqli_fetch_assoc($result_autor);
            $tipo_usuario = "autor";
        } else {
            // Buscar en tabla revisores
            $query_revisor = "SELECT rut_revisor AS rut, nombre_revisor AS nombre, correo_revisor AS correo, usuario_revisor AS usuario, contraseña_revisor AS password 
                              FROM Revisores 
                              WHERE usuario_revisor = '$usuario'";
            $result_revisor = mysqli_query($conexion, $query_revisor);
            if ($result_revisor && mysqli_num_rows($result_revisor) == 1) {
                $row = mysqli_fetch_assoc($result_revisor);
                $tipo_usuario = "revisor";
            } else {
                $row = null;
            }
        }

        if ($row) {
            // Verificar contraseña
            if (password_verify($password, $row["password"])) {
                $_SESSION["usuario_rut"] = $row["rut"];
                $_SESSION["usuario_nombre"] = $row["nombre"];
                $_SESSION["usuario_correo"] = $row["correo"];
                $_SESSION["usuario_tipo"] = $tipo_usuario;

                header("Location: home.php");
                exit();
            } else {
                $error_message = "Usuario o contraseña incorrectos.";
            }
        } else {
            $error_message = "Usuario o contraseña incorrectos.";
        }
    }
}

include("../HTML/login.html");

echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            let errorMessage = document.getElementById('error-message');
            if (errorMessage) {
                errorMessage.textContent = '" . htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') . "';
            }
        });
      </script>";
?>

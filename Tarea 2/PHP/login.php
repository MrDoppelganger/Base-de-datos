<?php
session_start(); // Inicia la sesión (si no está iniciada)

// Incluye el archivo de conexión a la base de datos
include("conexion.php");

// Variables para almacenar mensajes de error
$error_message = "";

// Procesa el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Valida y sanea los datos del formulario
    $usuario = mysqli_real_escape_string($conexion, $_POST["usuario"]);
    $password = $_POST["password"]; // No sanear la contraseña aquí, se hace después

    // Validaciones básicas (puedes agregar más)
    if (empty($usuario) || empty($password)) {
        $error_message = "Por favor, ingresa usuario y contraseña.";
    } else {
        // Consulta la base de datos para verificar las credenciales
        $query = "SELECT id, rol, password FROM usuarios WHERE usuario = '$usuario'";
        $result = mysqli_query($conexion, $query);

        if ($result && mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            $hashed_password = $row["password"];

            // Verifica la contraseña utilizando password_verify()
            if (password_verify($password, $hashed_password)) {
                // Inicio de sesión exitoso
                $_SESSION["usuario_id"] = $row["id"];
                $_SESSION["usuario_rol"] = $row["rol"];

                // Redirige a la página principal (home.php)
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

// Incluye el HTML del formulario de login
include("../HTML/login.html");

// Puedes pasar variables a login.html si es necesario (ejemplo)
echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            let errorMessage = document.getElementById('error-message');
            if (errorMessage) {
                errorMessage.textContent = '" . htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') . "';
            }
        });
      </script>";

?>
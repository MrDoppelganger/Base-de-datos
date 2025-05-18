<?php
//--------------------CONFIGURACIÓN INICIAL-----------------
// Iniciamos sesión y nos conectamos a la base de datos
session_start(); 
include("conexion.php");

// Variable para almacenar mensajes de error
$error_message = "";

//-------------------PROCESAMIENTO DEL FORMULARIO----------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtenemos los datos del formulario
    $usuario = $_POST["usuario"];
    $password = $_POST["password"];

    // Validamos de campos vacíos
    if (empty($usuario) || empty($password)) {
        $error_message = "Por favor, ingresa usuario y contraseña.";
    } else {
        //-------------------LOGGIN-------------------
        // Consulta prepara buscando autores (Esto nos protege contra inyeccion de SQL)
        $query_autor = "SELECT rut_autor AS rut, nombre_autor AS nombre, correo_autor AS correo,
                        usuario_autor AS usuario, contraseña_autor AS password 
                        FROM Autores 
                        WHERE usuario_autor = ?";
        
        // Procesamos la conuslta preparada vinculando los parametros para obtener el usuario del autor.
        $stmt_autor = $conexion->prepare($query_autor);
        $stmt_autor->bind_param("s", $usuario);
        $stmt_autor->execute();
        $result_autor = $stmt_autor->get_result();

        // Verificamos si encontramos un autor
        if ($result_autor && $result_autor->num_rows == 1) {
            $row = $result_autor->fetch_assoc();
            
            // Verificamos la contraseña con password_verify (seguro)
            if (password_verify($password, $row["password"])) {
                // Configuramos variables de sesión
                $_SESSION["usuario_rut"] = $row["rut"];
                $_SESSION["usuario_nombre"] = $row["nombre"];
                $_SESSION["usuario_correo"] = $row["correo"];
                $_SESSION["usuario_tipo"] = "autor";
                
                // Redirigimos al panel correspondiente
                header("Location: home_autor.php");
                exit();
            } else {
                $error_message = "Usuario o contraseña incorrectos.";
            }
        } 
        // Si no es autor, buscamos en revisores
        else {
            // Consulta prepara buscando revisores ((Esto nos protege contra inyeccion de SQL)
            $query_revisor = "SELECT rut_revisor AS rut, nombre_revisor AS nombre, correo_revisor AS correo, 
                              rol_revisor AS rol, usuario_revisor AS usuario, contraseña_revisor AS password 
                              FROM Revisores 
                              WHERE usuario_revisor = ?";
            
            // Procesamos la conuslta preparada vinculando los parametros para obtener el usuario del autor.
            $stmt_revisor = $conexion->prepare($query_revisor);
            $stmt_revisor->bind_param("s", $usuario);
            $stmt_revisor->execute();
            $result_revisor = $stmt_revisor->get_result();

            // Verificamos si encontramos un revisor
            if ($result_revisor && $result_revisor->num_rows == 1) {
                $row = $result_revisor->fetch_assoc();
                
                // Verificamos la contraseña
                if (password_verify($password, $row["password"])) {
                    // Configuramos variables de sesión
                    $_SESSION["usuario_rut"] = $row["rut"];
                    $_SESSION["usuario_nombre"] = $row["nombre"];
                    $_SESSION["usuario_correo"] = $row["correo"];

                    // proteccion en caso de discrepancia de datos(minúsculas, sin espacios)
                    $rol = strtolower(trim($row["rol"]));
                    
                    // Redirigimos según el rol
                    if ($rol == "admin") {
                        $_SESSION["usuario_tipo"] = "admin";
                        header("Location: home_admin.php");
                    } else {
                        $_SESSION["usuario_tipo"] = "revisor";
                        header("Location: home_revisor.php");
                    }
                    exit();
                } else {
                    $error_message = "Usuario o contraseña incorrectos.";
                }
            } else {
                $error_message = "Usuario o contraseña incorrectos.";
            }
            // Cerramos el statement del revisor
            $stmt_revisor->close();
        }
        // Cerramos el statement del autor
        $stmt_autor->close();
    }
}

//-------------------MOSTRAR FORMULARIO-------------------
// Incluimos el HTML del formulario de login
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
<?php
//--------------------inicialización-----------------
 //iniciamos sesion y nos conectamos a la base de datos.
 session_start();
 include("conexion.php");

 // Creamos una Array para almacenar los errores.
 $errores = array();

//-------------------Registro en la BD---------------
 if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtenemos y validamos los datos del formulario.
    $rut = $_POST["rut"];
    $nombre = $_POST["nombre"];
    $correo = $_POST["correo"];
    $tipo_usuario = $_POST["tipo_usuario"];
    $usuario = $_POST["usuario"];
    $password = $_POST["password"];

    //------------------Validaciones----------------------
    // Realizamos algunas validaciones de los campos.
    if (empty($rut) || empty($nombre) || empty($correo) || empty($tipo_usuario) || empty($usuario) || empty($password)) {
        $errores[] = "Todos los campos son obligatorios.";
    }
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electrónico no es válido.";
    }
    if (strlen($password) < 8) {
        $errores[] = "La contraseña debe tener al menos 8 caracteres.";
    }

    // Antes de registrar, verificamos que el nuevo usario no este en la tabla de revisores ni de autores segun el tipo de usuario.
    $tabla = ($tipo_usuario == "autor") ? "Autores" : "Revisores";
    $campo_usuario = ($tipo_usuario == "autor") ? "usuario_autor" : "usuario_revisor";
    $campo_rut = ($tipo_usuario == "autor") ? "rut_autor" : "rut_revisor";

    // Verificamos si existe el usuario
    $consulta_usuario = "SELECT COUNT(*) FROM $tabla WHERE $campo_usuario = '$usuario'";
    $resultado_usuario = mysqli_query($conexion, $consulta_usuario);
    $fila_usuario = mysqli_fetch_array($resultado_usuario);

    if ($fila_usuario[0] > 0) {
        $errores[] = "El usuario ya existe.";
    }

    // Verificamos si existe el RUT
    $consulta_rut = "SELECT COUNT(*) FROM $tabla WHERE $campo_rut = '$rut'";
    $resultado_rut = mysqli_query($conexion, $consulta_rut);
    $fila_rut = mysqli_fetch_array($resultado_rut);

    if ($fila_rut[0] > 0) {
        $errores[] = "El RUT ya existe.";
    }

    //----------------Insercion en la BD-----------------
    // Si todas las validaciones se cumplen, procedemos a hashear nuestra contraseña.
    if (count($errores) == 0) {
        $password_hasheado = password_hash($password, PASSWORD_DEFAULT);

        # Insertamos los autores.
        if ($tipo_usuario == "autor") {
            $insertar = "INSERT INTO Autores (rut_autor, nombre_autor, correo_autor, usuario_autor, contraseña_autor)
            VALUES ('$rut', '$nombre', '$correo', '$usuario', '$password_hasheado')";
        } 
        #Insertamos los revisores
        else {
            $insertar = "INSERT INTO Revisores (rut_revisor, nombre_revisor, correo_revisor, usuario_revisor, contraseña_revisor)
            VALUES ('$rut', '$nombre', '$correo', '$usuario', '$password_hasheado')";
        }

        #Informamos en caso de que se haya completado la inserición o no.
        if (mysqli_query($conexion, $insertar)) {
            $_SESSION['mensaje'] = "¡Registro exitoso! Ya puedes iniciar sesión.";
            header("Location: registro_exitoso.php");
            exit();
        }
         else {
            $errores[] = "Error al registrar el usuario: " . mysqli_error($conexion);
        }
    }
 }

 include("../HTML/registro.html");

 echo "<script>
  document.addEventListener('DOMContentLoaded', function() {
  let errorMessage = document.getElementById('error-message');
  if (errorMessage) {
  errorMessage.textContent = '" . htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') . "';
  }
  });
  </script>";

 ?>
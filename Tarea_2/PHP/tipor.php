<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rut = $_POST["rut"];
    $nombre = $_POST["nombre"];
    $correo = $_POST["correo"];
    $tipo_usuario = $_POST["tipo_usuario"]; // autor o revisor
    $usuario = $_POST["usuario"];
    $password = $_POST["password"];

    // Validacion
    if (empty($rut) || empty($nombre) || empty($correo) || empty($tipo_usuario) || empty($usuario) || empty($password)) {
        echo "Por favor, completa todos los campos.";
        exit;
    }

    // Hashear 
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // a tabla
    if ($tipo_usuario === "autor") {
        $sql = "INSERT INTO autores (rut_autor, nombre_autor, correo_autor, rol_autor, usuario_autor, contraseña_autor) VALUES (?, ?, ?, ?, ?, ?)";
        $rol = "autor"; 
    } else {
        $sql = "INSERT INTO revisores (rut_revisor, nombre_revisor, correo_revisor, rol_revisor, usuario_revisor, contraseña_revisor) VALUES (?, ?, ?, ?, ?, ?)";
        $rol = "revisor"; 
    }

    $stmt = mysqli_prepare($conexion, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssss", $rut, $nombre, $correo, $rol, $usuario, $password_hash);
        if (mysqli_stmt_execute($stmt)) {
            echo "Registro exitoso. <a href='login.php'>Inicia sesión aquí</a>";
        } else {
            echo "Error al registrar usuario.";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error al preparar la consulta.";
    }

    mysqli_close($conexion);
}
?>

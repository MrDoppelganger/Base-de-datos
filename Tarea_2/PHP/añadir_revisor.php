<?php
session_start();
include "conexion.php";

if (!isset($_SESSION["usuario_rut"]) || $_SESSION["usuario_tipo"] !== "admin") {
    header("Location: login.php");
    exit();
}

$error = "";
$especialidades = [];

// Obtener especialidades
$result_especialidades = $conexion->query("SELECT * FROM Especialidad_Topico");
if ($result_especialidades) {
    $especialidades = $result_especialidades->fetch_all(MYSQLI_ASSOC);
}

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];
    $rol = $_POST['rol'];
    $especialidades_seleccionadas = $_POST['especialidades'] ?? [];

    // Validaciones
    if (empty($nombre) || empty($correo) || empty($usuario) || empty($password)) {
        $error = "Todos los campos obligatorios deben ser completados";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "El formato del correo electrónico es inválido";
    } elseif (strlen($password) < 8) {
        $error = "La contraseña debe tener al menos 8 caracteres";
    } else {
        try {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Llamar al procedimiento almacenado
            $stmt = $conexion->prepare("CALL InsertarRevisor(?, ?, ?, ?, ?, @resultado)");
            $stmt->bind_param("sssss", $nombre, $correo, $usuario, $password_hash, $rol);
            $stmt->execute();
            
            $result = $conexion->query("SELECT @resultado AS resultado");
            $row = $result->fetch_assoc();
            
            if ($row['resultado'] == 'exito') {
                $rut_revisor = $conexion->insert_id;
                
                // Insertar especialidades
                if (!empty($especialidades_seleccionadas)) {
                    $stmt_esp = $conexion->prepare("INSERT INTO Especialidad_Revisores VALUES (?, ?)");
                    foreach ($especialidades_seleccionadas as $especialidad_id) {
                        $stmt_esp->bind_param("si", $rut_revisor, $especialidad_id);
                        $stmt_esp->execute();
                    }
                }
                
                $_SESSION['mensaje_exito'] = "Revisor agregado exitosamente. Correo enviado a: $correo";
                header("Location: home_admin.php");
                exit();
            } else {
                $error = "Error: " . $row['resultado'];
            }
        } catch (mysqli_sql_exception $e) {
            $error = "Error de base de datos: " . $e->getMessage();
        }
    }
}

// Incluir la vista
include "VIEW/añadir_revisor_view.php";
?>
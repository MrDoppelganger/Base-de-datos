<?php
session_start();
if (!isset($_SESSION["usuario_rut"]) || $_SESSION["usuario_tipo"] !== "admin") {
    header("Location: login.php");
    exit();
}

include "conexion.php";

if (isset($_GET['rut'])) {
    $rut = $_GET['rut'];
    
    // 1. Primero obtenemos los datos del revisor
    $query = "SELECT rol_revisor, correo_revisor, nombre_revisor FROM Revisores WHERE rut_revisor = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("s", $rut);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $revisor = $result->fetch_assoc();
        $correo = $revisor['correo_revisor'];
        $nombre = $revisor['nombre_revisor'];
        
        // 2. Validación en PHP para no eliminar admins
        if ($revisor['rol_revisor'] === 'admin') {
            $_SESSION['mensaje_error'] = "No se puede eliminar al administrador $nombre ($correo)";
            header("Location: home_admin.php");
            exit();
        }
        
        // 3. Intentamos la eliminación (el trigger es nuestra segunda línea de defensa)
        try {
            // Iniciamos transacción para operaciones atómicas
            $conexion->begin_transaction();
            
            // Primero eliminamos las especialidades asociadas
            $query_especialidades = "DELETE FROM Especialidad_Revisores WHERE rut_revisor = ?";
            $stmt_especialidades = $conexion->prepare($query_especialidades);
            $stmt_especialidades->bind_param("s", $rut);
            $stmt_especialidades->execute();
            
            // Luego eliminamos al revisor
            $query_delete = "DELETE FROM Revisores WHERE rut_revisor = ?";
            $stmt_delete = $conexion->prepare($query_delete);
            $stmt_delete->bind_param("s", $rut);
            $stmt_delete->execute();
            
            // Confirmamos la transacción
            $conexion->commit();
            
            // Mensaje de éxito
            $_SESSION['mensaje_exito'] = "Se ha eliminado al revisor $nombre y se ha enviado un correo a $correo informando su eliminación";
            
        } catch (mysqli_sql_exception $exception) {
            $conexion->rollback();
            
            // Capturamos el error específico del trigger
            if ($exception->getCode() == 1644) { // Código para errores personalizados
                $_SESSION['mensaje_error'] = $exception->getMessage();
            } else {
                $_SESSION['mensaje_error'] = "Error al eliminar el revisor: " . $exception->getMessage();
            }
        }
    } else {
        $_SESSION['mensaje_error'] = "Revisor no encontrado";
    }
    
    header("Location: home_admin.php");
    exit();
}
?>
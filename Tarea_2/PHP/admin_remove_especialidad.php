<?php
 session_start();
 if (!isset($_SESSION["usuario_rut"]) || $_SESSION["usuario_tipo"] !== "admin") {
  header("Location: login.php");
  exit();
 }

 include("conexion.php");

 // Verificar que se reciba el RUT y el ID de la especialidad
 if (!isset($_GET['rut']) || !isset($_GET['id'])) {
  die("No se especificó el revisor o la especialidad a eliminar.");
 }

 $rut = $_GET['rut'];
 $id_especialidad = $_GET['id'];

 // Eliminar la especialidad del revisor
 $query = "DELETE FROM Especialidad_Revisores WHERE rut_revisor = ? AND id_especialidad_topico = ?";
 $stmt = $conexion->prepare($query);
 $stmt->bind_param("si", $rut, $id_especialidad);

 if ($stmt->execute()) {
  // Redirigir de vuelta a la página de edición con un mensaje de éxito
  $_SESSION['mensaje_exito'] = "Especialidad eliminada correctamente.";
  header("Location: admin_edit_revisor.php?rut=" . urlencode($rut));
  exit();
 } else {
  // Redirigir con un mensaje de error
  $_SESSION['mensaje_error'] = "Error al eliminar la especialidad: " . $stmt->error;
  header("Location: admin_edit_revisor.php?rut=" . urlencode($rut));
  exit();
 }

 $stmt->close();
 $conexion->close();
 ?>
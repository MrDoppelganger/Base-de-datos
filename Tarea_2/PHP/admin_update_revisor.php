<?php
 session_start();
 if (!isset($_SESSION["usuario_rut"]) || $_SESSION["usuario_tipo"] !== "admin") {
  header("Location: login.php");
  exit();
 }

 include("conexion.php");

 if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Recoger y limpiar los datos del formulario
  $rut_revisor = trim($_POST['rut_revisor']);
  $correo_revisor = trim($_POST['correo_revisor']);
  $rol_revisor = $_POST['rol_revisor'];
  $usuario_revisor = trim($_POST['usuario_revisor']);
  $contraseña_revisor = $_POST['contraseña_revisor']; //  Puede estar vacío

  // Validaciones básicas (puedes agregar más validaciones si es necesario)
  if (empty($correo_revisor) || empty($rol_revisor) || empty($usuario_revisor)) {
  $_SESSION['mensaje_error'] = "Todos los campos obligatorios deben ser completados.";
  header("Location: admin_edit_revisor.php?rut=" . urlencode($rut_revisor));
  exit();
  } elseif (!filter_var($correo_revisor, FILTER_VALIDATE_EMAIL)) {
  $_SESSION['mensaje_error'] = "El formato del correo electrónico no es válido.";
  header("Location: admin_edit_revisor.php?rut=" . urlencode($rut_revisor));
  exit();
  }

  // Actualizar datos del revisor
  $query = "UPDATE Revisores SET correo_revisor = ?, rol_revisor = ?, usuario_revisor = ?";
  $params = [$correo_revisor, $rol_revisor, $usuario_revisor];
  $types = "sss";

  // Si se proporciona una nueva contraseña, actualizarla
  if (!empty($contraseña_revisor)) {
  $query .= ", contraseña_revisor = ?";
  $params[] = password_hash($contraseña_revisor, PASSWORD_DEFAULT); //  Hashear la nueva contraseña
  $types .= "s";
  }

  $query .= " WHERE rut_revisor = ?";
  $params[] = $rut_revisor;
  $types .= "s";

  $stmt = $conexion->prepare($query);
  $stmt->bind_param($types, ...$params); //  Usar spread operator para pasar los parámetros

  if ($stmt->execute()) {
  $_SESSION['mensaje_exito'] = "Datos del revisor actualizados correctamente.";
  header("Location: home_admin.php"); //  O redirigir a donde sea apropiado
  exit();
  } else {
  $_SESSION['mensaje_error'] = "Error al actualizar los datos del revisor: " . $stmt->error;
  header("Location: admin_edit_revisor.php?rut=" . urlencode($rut_revisor));
  exit();
  }

  $stmt->close();
  $conexion->close();

 } else {
  // Si no es una solicitud POST, redirigir (o mostrar un error)
  header("Location: home_admin.php"); //  O donde sea apropiado
  exit();
 }
 ?>
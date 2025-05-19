<?php
 session_start();
 if (!isset($_SESSION["usuario_rut"]) || $_SESSION["usuario_tipo"] !== "admin") {
  header("Location: login.php");
  exit();
 }

 include("conexion.php");

 if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Recoger y limpiar los datos básicos del revisor
  $rut_revisor = trim($_POST['rut_revisor']);
  $correo_revisor = trim($_POST['correo_revisor']);
  $rol_revisor = $_POST['rol_revisor'];
  $usuario_revisor = trim($_POST['usuario_revisor']);
  $contraseña_revisor = $_POST['contraseña_revisor'];

  // Recoger la nueva especialidad seleccionada
  $nueva_especialidad_id = $_POST['nueva_especialidad'];

  // Validaciones básicas para los datos del revisor
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
  $query_update_revisor = "UPDATE Revisores SET correo_revisor = ?, rol_revisor = ?, usuario_revisor = ?";
  $params_revisor = [$correo_revisor, $rol_revisor, $usuario_revisor];
  $types_revisor = "sss";

  if (!empty($contraseña_revisor)) {
  $query_update_revisor .= ", contraseña_revisor = ?";
  $params_revisor[] = password_hash($contraseña_revisor, PASSWORD_DEFAULT);
  $types_revisor .= "s";
  }

  $query_update_revisor .= " WHERE rut_revisor = ?";
  $params_revisor[] = $rut_revisor;
  $types_revisor .= "s";

  $stmt_update_revisor = $conexion->prepare($query_update_revisor);
  $stmt_update_revisor->bind_param($types_revisor, ...$params_revisor);

  if ($stmt_update_revisor->execute()) {
  $actualizacion_exitosa = true;
  } else {
  $_SESSION['mensaje_error'] = "Error al actualizar los datos del revisor: " . $stmt_update_revisor->error;
  $actualizacion_exitosa = false;
  }
  $stmt_update_revisor->close();

  // Añadir la nueva especialidad si se seleccionó una
  if (!empty($nueva_especialidad_id) && $actualizacion_exitosa) {
  // Verificar si la especialidad ya está asignada al revisor (opcional)
  $query_check_especialidad = "SELECT * FROM Especialidad_Revisores WHERE rut_revisor = ? AND id_especialidad_topico = ?";
  $stmt_check_especialidad = $conexion->prepare($query_check_especialidad);
  $stmt_check_especialidad->bind_param("si", $rut_revisor, $nueva_especialidad_id);
  $stmt_check_especialidad->execute();
  $result_check_especialidad = $stmt_check_especialidad->get_result();

  if ($result_check_especialidad->num_rows == 0) {
  // Insertar la nueva especialidad
  $query_insert_especialidad = "INSERT INTO Especialidad_Revisores (rut_revisor, id_especialidad_topico) VALUES (?, ?)";
  $stmt_insert_especialidad = $conexion->prepare($query_insert_especialidad);
  $stmt_insert_especialidad->bind_param("si", $rut_revisor, $nueva_especialidad_id);

  if ($stmt_insert_especialidad->execute()) {
  $_SESSION['mensaje_exito'] = "Datos del revisor actualizados y especialidad añadida correctamente.";
  } else {
  $_SESSION['mensaje_error'] = "Datos del revisor actualizados, pero error al añadir la especialidad: " . $stmt_insert_especialidad->error;
  }
  $stmt_insert_especialidad->close();
  } else {
  $_SESSION['mensaje_exito'] = "Datos del revisor actualizados. La especialidad seleccionada ya estaba asignada.";
  }
  $stmt_check_especialidad->close();
  } elseif ($actualizacion_exitosa && empty($nueva_especialidad_id)) {
  $_SESSION['mensaje_exito'] = "Datos del revisor actualizados correctamente.";
  }

  header("Location: admin_edit_revisor.php?rut=" . urlencode($rut_revisor));
  exit();

 } else {
  // Si no es una solicitud POST
  header("Location: home_admin.php");
  exit();
 }
 ?>
<?php
 session_start();
 include "conexion.php";

 // Verificar sesión y permisos
 if (!isset($_SESSION["usuario_rut"]) || $_SESSION["usuario_tipo"] !== "admin") {
  header("Location: login.php");
  exit();
 }

 $error = $exito = "";
 $especialidades = [];

 // Obtener todas las especialidades disponibles
 $query_especialidades = "SELECT * FROM Especialidad_Topico";
 $result_especialidades = $conexion->query($query_especialidades);
 if ($result_especialidades) {
  $especialidades = $result_especialidades->fetch_all(MYSQLI_ASSOC);
 }

 // Procesar formulario
 if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $rut_revisor = trim($_POST['rut_revisor']); // Nuevo campo RUT Revisor
  $nombre = trim($_POST['nombre']);
  $correo = trim($_POST['correo']);
  $usuario = trim($_POST['usuario']);
  $password = $_POST['password'];
  $rol = $_POST['rol'];
  $especialidades_seleccionadas = $_POST['especialidades'] ?? [];

  // Validaciones básicas
  if (empty($rut_revisor) || empty($nombre) || empty($correo) || empty($usuario) || empty($password)) {
  $error = "Todos los campos obligatorios deben ser completados";
  } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
  $error = "El formato del correo electrónico es inválido";
  } elseif (strlen($password) < 8) {
  $error = "La contraseña debe tener al menos 8 caracteres";
  } else {
  try {
  // Verificar si el RUT ya existe
  $stmt_check_rut = $conexion->prepare("SELECT rut_revisor FROM Revisores WHERE rut_revisor = ?");
  $stmt_check_rut->bind_param("s", $rut_revisor);
  $stmt_check_rut->execute();
  $result_check_rut = $stmt_check_rut->get_result();
  if ($result_check_rut->num_rows > 0) {
  $error = "Ya existe un revisor con ese RUT.";
  } else {
  // Hashear la contraseña
  $password_hash = password_hash($password, PASSWORD_DEFAULT);

  // Insertar el nuevo revisor
  $stmt = $conexion->prepare("INSERT INTO Revisores (rut_revisor, nombre_revisor, correo_revisor, usuario_revisor, contraseña_revisor, rol_revisor) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssss", $rut_revisor, $nombre, $correo, $usuario, $password_hash, $rol);

  if ($stmt->execute()) {
  // Insertar especialidades
  if (!empty($especialidades_seleccionadas)) {
  $stmt_esp = $conexion->prepare("INSERT INTO Especialidad_Revisores (rut_revisor, id_especialidad_topico) VALUES (?, ?)");
  foreach ($especialidades_seleccionadas as $especialidad_id) {
  $stmt_esp->bind_param("si", $rut_revisor, $especialidad_id);
  $stmt_esp->execute();
  }
  $stmt_esp->close();
  }

  $_SESSION['mensaje_exito'] = "Revisor agregado exitosamente. Se ha enviado un correo a $correo";
  header("Location: home_admin.php");
  exit();
  } else {
  $error = "Error al agregar el revisor: " . $stmt->error;
  }
  $stmt->close();
  }
  $stmt_check_rut->close();

  } catch (mysqli_sql_exception $e) {
  $error = "Error en la base de datos: " . $e->getMessage();
  }
  }
 }
 ?>

 <!DOCTYPE html>
 <html lang="es">
 <head>
  <meta charset="UTF-8">
  <title>Añadir Nuevo Revisor</title>
  <link rel="stylesheet" href="../Public/CSS/admin.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
  .form-container {
  position: relative;
  max-width: 800px;
  margin: 50px auto;
  padding: 40px;
  background-color: #bb97bd;
  border: 2px solid #a38ca3;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  }

  .form-container::before {
  content: "";
  position: absolute;
  top: -27px;
  left: 50%;
  width: calc(100% + 4px);
  height: 40px;
  background-color: #50295c;
  border-radius: 10px 10px 0 0;
  transform: translateX(-50%);
  z-index: 1;
  }

  .form-container::after {
  content: "";
  position: absolute;
  top: -20px;
  left: 20px;
  width: 20px;
  height: 20px;
  background-color: #d430d4;
  border-radius: 50%;
  box-shadow: 30px 0 0 #d430d4, 60px 0 0 #d430d4;
  z-index: 2;
  }

  .especialidades-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 10px;
  margin: 20px 0;
  }

  .especialidad-item {
  background: rgba(255, 255, 255, 0.1);
  padding: 10px;
  border-radius: 5px;
  }
  /* ------------------ Botón de Volver ------------------ */
  .volver-container {
  position: absolute;
  top: 15px;
  left: 15px;
  }

  .volver-link {
  display: inline-block;
  padding: 20px 18px;
  font-size: 1em;
  text-align: center;
  font-weight: bold;
  background-color: #6d1a33;
  color: white;
  border-radius: 8px;
  text-decoration: none;
  transition: all 0.3s ease;
  }

  .volver-link:hover {
  background-color: #6d1a33;
  transform: translateY(-2px);
  }

  </style>
 </head>
 <body class="pagina-admin">
  <div class="volver-container">
  <a href="../PHP/home_admin.php" class="volver-link">🏠 Volver al Home</a>
  </div>

  <div class="form-container">
  <h2 class="text-center mb-4">Añadir Nuevo Revisor</h2>

  <?php if ($error): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST">
  <div class="mb-3">
  <label class="form-label">RUT Revisor:</label>
  <input type="text" name="rut_revisor" class="form-control" required>
  </div>

  <div class="mb-3">
  <label class="form-label">Nombre completo:</label>
  <input type="text" name="nombre" class="form-control" required>
  </div>

  <div class="mb-3">
  <label class="form-label">Correo electrónico:</label>
  <input type="email" name="correo" class="form-control" required>
  </div>

  <div class="mb-3">
  <label class="form-label">Nombre de usuario:</label>
  <input type="text" name="usuario" class="form-control" required>
  </div>

  <div class="mb-3">
  <label class="form-label">Contraseña:</label>
  <input type="password" name="password" class="form-control" required>
  </div>

  <div class="mb-3">
  <label class="form-label">Rol:</label>
  <select name="rol" class="form-select" required>
  <option value="revisor">Revisor</option>
  <option value="admin">Administrador</option>
  </select>
  </div>

  <div class="mb-4">
  <label class="form-label">Especialidades:</label>
  <div class="especialidades-container">
  <?php foreach ($especialidades as $especialidad): ?>
  <div class="especialidad-item">
  <label>
  <input type="checkbox" name="especialidades[]"
  value="<?= $especialidad['id_especialidad_topico'] ?>">
  <?= htmlspecialchars($especialidad['tipo']) ?>
  </label>
  </div>
  <?php endforeach; ?>
  </div>
  </div>

  <button type="submit" class="btn btn-primary w-100">
  <i class="fas fa-user-plus"></i> Registrar Revisor
  </button>
  </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
 </body>
 </html>
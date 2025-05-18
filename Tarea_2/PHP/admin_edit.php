<?php
session_start();
if (!isset($_SESSION["usuario_rut"]) || $_SESSION["usuario_tipo"] !== "admin") {
    header("Location: login.php");
    exit();
}
include("conexion.php");

// Verificar que se reciba el RUT del revisor a editar
if (!isset($_GET['rut'])) {
    die("No se especificó el revisor a editar.");
}
$rut = $_GET['rut'];

// Obtener datos del revisor
$query = "SELECT * FROM Revisores WHERE rut_revisor = '$rut'";
$result = $conexion->query($query);
if ($result->num_rows == 0) {
    die("Revisor no encontrado.");
}
$revisor = $result->fetch_assoc();

// Obtener las especialidades actuales del revisor
$query_specialties = "
    SELECT er.id_especialidad_topico, et.tipo, et.descripcion 
    FROM Especialidad_Revisores er 
    JOIN Especialidad_Topico et 
        ON er.id_especialidad_topico = et.id_especialidad_topico 
    WHERE er.rut_revisor = '$rut'
";
$result_specialties = $conexion->query($query_specialties);
$especialidades_actuales = array();
while ($row = $result_specialties->fetch_assoc()){
    $especialidades_actuales[] = $row;
}

// Obtener la lista completa de especialidades
$query_all = "SELECT * FROM Especialidad_Topico";
$result_all = $conexion->query($query_all);
$all_especialidades = array();
while ($row = $result_all->fetch_assoc()){
    $all_especialidades[] = $row;
}

// Calcular especialidades disponibles
$especialidades_disponibles = array();
foreach ($all_especialidades as $especialidad) {
    $found = false;
    foreach ($especialidades_actuales as $actual) {
        if ($actual['id_especialidad_topico'] == $especialidad['id_especialidad_topico']) {
            $found = true;
            break;
        }
    }
    if (!$found) {
        $especialidades_disponibles[] = $especialidad;
    }
}

// Obtener los artículos donde el revisor participó
$query_articulos = "
    SELECT a.id_articulo, a.titulo, a.estado, r.fecha_revision
    FROM Revision r
    JOIN Articulos a ON r.id_articulo = a.id_articulo
    WHERE r.rut_revisor = '$rut'
";
$result_articulos = $conexion->query($query_articulos);
$articulos_revisor = array();
while ($row = $result_articulos->fetch_assoc()){
    $articulos_revisor[] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Revisor</title>
    <link rel="stylesheet" href="../Public/CSS/estiloeditadmin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="volver-container">
    <a href="home_admin.php" class="volver-link">Volver</a>
</div>

<div class="contenedores-wrapper">
    <!-- CONTENEDOR IZQUIERDO: Datos y Especialidades -->
    <div class="contenedor-gemelo">
        <h1>Editar revisor</h1>
        <form action="admin_update_revisor.php" method="POST">
            <label for="rut">RUT:</label>
            <input type="text" id="rut" name="rut_revisor" value="<?= htmlspecialchars($revisor['rut_revisor']) ?>" readonly class="no-edit">
            
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre_revisor" value="<?= htmlspecialchars($revisor['nombre_revisor']) ?>" readonly class="no-edit">
            
            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo_revisor" value="<?= htmlspecialchars($revisor['correo_revisor']) ?>" required>
            
            <label for="rol">Rol:</label>
            <select name="rol_revisor" id="rol">
                <option value="revisor" <?= ($revisor['rol_revisor'] === 'revisor') ? 'selected' : '' ?>>Revisor</option>
                <option value="admin" <?= ($revisor['rol_revisor'] === 'admin') ? 'selected' : '' ?>>Admin</option>
            </select>
            
            <label for="usuario">Usuario:</label>
            <input type="text" id="usuario" name="usuario_revisor" value="<?= htmlspecialchars($revisor['usuario_revisor']) ?>" required>
            
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="contraseña_revisor" placeholder="Dejar en blanco para no cambiar">
            
            <h3>Especialidades Actuales</h3>
            <table class="especialidades-table">
                <thead>
                    <tr>
                        <th>Especialidad</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(count($especialidades_actuales) > 0): ?>
                    <?php foreach($especialidades_actuales as $esp): ?>
                    <tr>
                        <td><?= htmlspecialchars($esp['tipo']) ?></td>
                        <td>
                            <a href="admin_remove_especialidad.php?rut=<?= urlencode($rut) ?>&id=<?= $esp['id_especialidad_topico'] ?>"
                               onclick="return confirm('¿Seguro deseas eliminar esta especialidad?');"
                               class="btn btn-danger btn-sm">Eliminar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2">Sin especialidades asignadas.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
            
            <h3>Añadir Especialidad</h3>
            <label for="nueva_especialidad">Selecciona una especialidad a agregar:</label>
            <select name="nueva_especialidad" id="nueva_especialidad">
                <option value="">-- Ninguna --</option>
                <?php foreach($especialidades_disponibles as $esp): ?>
                <option value="<?= $esp['id_especialidad_topico'] ?>">
                    <?= htmlspecialchars($esp['tipo']) ?> - <?= htmlspecialchars($esp['descripcion']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit" class="btn-save">Guardar Cambios</button>
        </form>
    </div>
    
    <!-- CONTENEDOR DERECHO: Revisiones -->
    <div class="contenedor-gemelo">
        <h3>Revisiones del Revisor</h3>
        <?php if(count($articulos_revisor) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>ID Artículo</th>
                    <th>Título</th>
                    <th>Estado</th>
                    <th>Fecha Revisión</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($articulos_revisor as $art): ?>
                <tr>
                    <td><?= htmlspecialchars($art['id_articulo']) ?></td>
                    <td><?= htmlspecialchars($art['titulo']) ?></td>
                    <td><?= htmlspecialchars($art['estado']) ?></td>
                    <td><?= htmlspecialchars($art['fecha_revision']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>No hay revisiones registradas para este revisor.</p>
        <?php endif; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
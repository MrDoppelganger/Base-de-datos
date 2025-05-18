<?php
session_start();
if (!isset($_SESSION["usuario_rut"]) || $_SESSION["usuario_tipo"] !== "admin") {
    header("Location: login.php");
    exit();
}
include "conexion.php";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Administrador</title>
    <link rel="stylesheet" href="../Public/CSS/admin.css">
    <script src="https://kit.fontawesome.com/23ed4a8228.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function confirmarEliminacion(rut) {
            if (confirm("¿Seguro que deseas eliminar este revisor?\nEsta acción no se puede deshacer.")) {
                window.location.href = "eliminar_revisor.php?rut=" + encodeURIComponent(rut);
            }
            return false;
        }
    </script>
</head>
<body class="pagina-admin">
    <!-- Mensajes de sesión -->
    <?php if (isset($_SESSION['mensaje_exito'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['mensaje_exito']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['mensaje_exito']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['mensaje_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['mensaje_error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['mensaje_error']); ?>
    <?php endif; ?>

    <a href="logout.php" class="btn-cerrar-sesion">
        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
    </a>
    
    <div class="ver-revisores">
        <h2>Bienvenido, <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Admin') ?></h2>
        <h3>Información de Revisores</h3>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Tópicos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // CONSULTA SEGURA con prepared statements
                    $query = "
                        SELECT r.rut_revisor, r.nombre_revisor, r.correo_revisor, 
                               r.usuario_revisor, r.rol_revisor,
                               GROUP_CONCAT(et.tipo SEPARATOR ', ') AS topicos
                        FROM Revisores r
                        LEFT JOIN Especialidad_Revisores er ON r.rut_revisor = er.rut_revisor
                        LEFT JOIN Especialidad_Topico et ON er.id_especialidad_topico = et.id_especialidad_topico
                        GROUP BY r.rut_revisor
                        ORDER BY r.rol_revisor DESC, r.nombre_revisor ASC
                    ";
                    
                    $result = $conexion->query($query);
                    
                    if ($result->num_rows > 0) {
                        while($revisor = $result->fetch_object()) {
                            $esAdmin = ($revisor->rol_revisor === 'admin');
                    ?>
                    <tr class="<?= $esAdmin ? 'table-warning' : '' ?>">
                        <td><?= htmlspecialchars($revisor->nombre_revisor) ?>
                            <?= $esAdmin ? '<span class="badge bg-danger ms-2">Admin</span>' : '' ?>
                        </td>
                        <td><?= htmlspecialchars($revisor->correo_revisor) ?></td>
                        <td><?= htmlspecialchars($revisor->usuario_revisor) ?></td>
                        <td><?= htmlspecialchars($revisor->rol_revisor) ?></td>
                        <td><?= htmlspecialchars($revisor->topicos ?? "Sin especialidad") ?></td>
                        <td class="acciones">
                            <a href="admin_edit.php?rut=<?= urlencode($revisor->rut_revisor) ?>" 
                               class="btn btn-sm btn-primary" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" onclick="return confirmarEliminacion('<?= htmlspecialchars($revisor->rut_revisor) ?>')" 
                               class="btn btn-sm btn-danger <?= $esAdmin ? 'disabled' : '' ?>" 
                               title="<?= $esAdmin ? 'No se pueden eliminar administradores' : 'Eliminar' ?>">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="6" class="text-center">No hay revisores registrados</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        
        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
            <a href="añadir_revisor.php" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> Añadir Revisor
            </a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir Revisor</title>
    <link rel="stylesheet" href="../Public/CSS/admin.css">
    <link rel="stylesheet" href="../Public/CSS/añadir.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="pagina-admin">
    <a href="home_admin.php" class="btn-volver">
        <i class="fas fa-arrow-left"></i> Volver
    </a>

    <div class="form-container">
        <h2 class="text-center mb-4">Añadir Nuevo Revisor</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nombre completo:</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Correo electrónico:</label>
                <input type="email" name="correo" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Usuario:</label>
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
                    <?php foreach ($especialidades as $esp): ?>
                        <div class="especialidad-item">
                            <label>
                                <input type="checkbox" name="especialidades[]" 
                                    value="<?= $esp['id_especialidad_topico'] ?>">
                                <?= htmlspecialchars($esp['tipo']) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-user-plus"></i> Registrar
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
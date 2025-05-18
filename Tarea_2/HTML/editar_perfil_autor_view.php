<?php
session_start();
$autor = $_SESSION["autor_data"] ?? [];
$error = $_SESSION["error"] ?? "";
$mensaje = $_SESSION["mensaje"] ?? "";
unset($_SESSION["error"], $_SESSION["mensaje"]); // Limpiar variables después de mostrarlas
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="../Public/CSS/estiloedicion.css">

</head>
<body>
    <div class="volver-container">
        <a href="../PHP/home_autor.php" class="volver-link">🏠 Volver al Home</a>
    </div>

    <div class="editar-container">
        <h1>Editar Perfil</h1>

        <?php if ($error): ?>
            <div style="color: red;"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if ($mensaje): ?>
            <div style="color: green;"><?= $mensaje ?></div>
        <?php endif; ?>

        <form action="../PHP/editar_perfil_autor.php" method="POST">
            <div>
                <label>RUT:</label>
                <input type="text" value="<?= $autor['rut_autor'] ?? '' ?>" readonly>
            </div>
            
            <div>
                <label>Nombre:</label>
                <input type="text" name="nombre" value="<?= $autor['nombre_autor'] ?? '' ?>" required>
            </div>
            
            <div>
                <label>Correo:</label>
                <input type="email" name="correo" value="<?= $autor['correo_autor'] ?? '' ?>" required>
            </div>
            
            <div>
                <label>Rol:</label>
                <input type="text" value="<?= $autor['rol_autor'] ?? '' ?>" readonly>
            </div>
            
            <div>
                <label>Usuario:</label>
                <input type="text" name="usuario" value="<?= $autor['usuario_autor'] ?? '' ?>" required>
            </div>
            
            <div>
                <label>Nueva contraseña (opcional):</label>
                <input type="password" name="nueva_contrasena">
            </div>
            
            <button type="submit">Guardar Cambios</button>
        </form>

        <div>
            <h3>Eliminar mi cuenta</h3>
            <form action="../PHP/eliminar_cuenta_autor.php" method="POST">
                <button type="submit">Eliminar Cuenta</button>
            </form>
        </div>
    </div>

</body>
</html>

<?php
session_start();
$mensaje = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : "Registro completado.";
unset($_SESSION['mensaje']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Publicacion Exitoso</title>
    <link rel="stylesheet" href="../Public/CSS/exito.css">
</head>
<body>
    <div class="card">
        <h1>🎉 ¡Publicación Exitoso!</h1>
        <p><?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></p>
        <a href="home_autor.php" class="boton">Volver al Home</a>
    </div>
</body>
</html>

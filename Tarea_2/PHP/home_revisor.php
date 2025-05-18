<?php
session_start();
if (!isset($_SESSION["usuario_rut"]) || $_SESSION["usuario_tipo"] !== "revisor") {
    header("Location: login.php");
    exit();
}
$nombre_usuario = htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Autor');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Autor - <?php echo $nombre_usuario; ?></title>
    <link rel="stylesheet" href="../Public/CSS/botonhome.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="pagina-autor">
    <nav class="panel-botones">
        <a href="editar_perfil_revisor.php">
           <i class="fa-solid fa-circle-user"></i> Editar Perfil
        </a>
        <a href="crear_articulo.php">
            <i class="fa-solid fa-address-book"></i>  Crear Artículo
        </a>
        <a href="ver_articulos_revisor.php">
            <i class="fa-solid fa-file"></i>  Artículos Revisados
        </a>
        <a href="logout.php" class="cerrar-sesion">
            <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
        </a>
    </nav>
</body>
</html>
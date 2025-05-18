<?php
session_start();
if (!isset($_SESSION["usuario_rut"]) || $_SESSION["usuario_tipo"] !== "autor") {
    header("Location: login.php");
    exit();
}

// Obtener datos del autor logueado
include "conexion.php";
$rut_autor = $_SESSION["usuario_rut"];
$sql_autor = $conexion->query("SELECT * FROM Autores WHERE rut_autor = '$rut_autor'");
$autor_loguedo = $sql_autor->fetch_object();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Autor</title>
    <link rel="stylesheet" href="../Public/CSS/estilocreararticulo.css">
    <script src="https://kit.fontawesome.com/23ed4a8228.js" crossorigin="anonymous"></script>
</head>
<body class="pagina-autor">
    <div class="volver-container">
        <a href="../PHP/home_autor.php" class="volver-link">🏠 Volver al Home</a>
    </div>

    <a href="logout.php" class="btn-cerrar-sesion">Cerrar sesión</a>
    <div class="panel-autor">
        <div class="contenedor-principal">
            <!-- Formulario a la izquierda -->
            <div class="formulario-articulo">
                <h2>Bienvenido, <?= htmlspecialchars($autor_loguedo->nombre_autor ?? 'Autor') ?></h2>
                <form action="procesar_articulo.php" method="post">
                    <h3>Datos del Artículo</h3>
                    <label>Título:</label>
                    <input type="text" name="titulo" required><br>

                    <label>Resumen:</label>
                    <textarea name="resumen" required></textarea><br>

                    <label>Tópicos (separados por coma):</label>
                    <input type="text" name="topicos" required><br>

                    <h3>Autores del artículo</h3>
                    <!-- Autor 1 (logueado) -->
                    <div class="autor-fijo">
                        <label>Autor 1 (Tu):</label>
                        <input type="text" value="<?= htmlspecialchars($autor_loguedo->nombre_autor) ?>" readonly>
                        <input type="hidden" name="autor[0][nombre]" value="<?= htmlspecialchars($autor_loguedo->nombre_autor) ?>">
                        <input type="hidden" name="autor[0][email]" value="<?= htmlspecialchars($autor_loguedo->correo_autor) ?>">
                    </div>

                    <!-- Autor 2 (opcional) -->
                    <div class="autor-opcional">
                        <label>Autor 2 (Opcional):</label>
                        <input type="text" name="autor[1][nombre]">
                        <label>Email:</label>
                        <input type="email" name="autor[1][email]">
                    </div>

                    <!-- Autor 3 (opcional) -->
                    <div class="autor-opcional">
                        <label>Autor 3 (Opcional):</label>
                        <input type="text" name="autor[2][nombre]">
                        <label>Email:</label>
                        <input type="email" name="autor[2][email]">
                    </div>
                    
                    <label>Selecciona autor de contacto:</label>
                    <select name="autor_contacto_index" required>
                        <option value="0">Autor 1 (<?= htmlspecialchars($autor_loguedo->nombre_autor) ?>)</option>
                        <option value="1">Autor 2</option>
                        <option value="2">Autor 3</option>
                    </select><br>

                    <input type="submit" value="Enviar artículo">
                </form>
            </div>
            
            <div class="tabla-articulos">
                <h3>Tópicos de artículos</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">N° Tópico</th>
                            <th scope="col">Tópico</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = $conexion->query("SELECT * FROM Especialidad_Topico");
                        while($datos = $sql->fetch_object()) { 
                        ?>
                        <tr>
                            <td><?= $datos->id_especialidad_topico ?></td>
                            <td><?= $datos->tipo ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

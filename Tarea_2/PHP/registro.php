<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="../Public/CSS/estilos.css">
</head>
<body>

    <div class="login-container">
        <h2>Registro de Usuario</h2>

        <form action="tipor.php" method="post">
            <div class="rut">
                <label for="rut">RUT:</label>
                <input type="text" name="rut" required>
            </div>

            <div class="form-nombre">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" required>
            </div>

            <div class="correo">
                <label for="correo">Correo:</label>
                <input type="email" name="correo" required>
            </div>

            <div class="tipo">
                <label for="tipo_usuario">Tipo de Usuario:</label><br>
                <input type="radio" id="autor" name="tipo_usuario" value="autor" required>
                <label for="autor">Autor</label>
                <input type="radio" id="revisor" name="tipo_usuario" value="revisor">
                <label for="revisor">Revisor</label>
            </div>

            <div class="usuario">
                <label for="usuario">Usuario:</label>
                <input type="text" name="usuario" required>
            </div>

            <div class="pass">
                <label for="password">Contraseña:</label>
                <input type="password" name="password" required>
            </div>

            <div class="cuenta">
                <input type="submit" value="Registrarse">
            </div>

            <p>¿Ya tienes cuenta? <a href="login.php">Inicia Sesión</a></p>
        </form>
    </div>

</body>
</html>

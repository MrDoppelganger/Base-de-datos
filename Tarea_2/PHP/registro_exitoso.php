<?php
session_start();
$mensaje = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : "Registro completado.";
unset($_SESSION['mensaje']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro Exitoso</title>
    <style>
        body {
            background-image: url('../Public/IMG/background.png');
            background-size: cover;
            background-repeat: no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .card {
            background-color: white;
            padding: 30px;
            margin: auto;
            max-width: 500px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0,0,0,0.1);
        }
        .boton {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 25px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .boton:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>🎉 ¡Registro Exitoso!</h1>
        <p><?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></p>
        <a href="login.php" class="boton">Ir a Iniciar Sesión</a>
    </div>
</body>
</html>

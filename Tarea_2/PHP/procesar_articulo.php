<?php
session_start();
include("conexion.php");

if (!isset($_SESSION["usuario_rut"]) || $_SESSION["usuario_tipo"] !== "autor") {
    header("Location: login.php");
    exit();
}

// Recoger datos
$titulo = trim($_POST["titulo"]);
$resumen = trim($_POST["resumen"]);
$topicos_raw = $_POST["topicos"];
$autores = $_POST["autor"];
$autor_contacto_index = intval($_POST["autor_contacto_index"]);

$fecha_envio = date("Y-m-d");
$estado = "enviado";

// ver que el titulo no exista
$titulo_normalizado = strtolower(trim($titulo));
$check_titulo = mysqli_query($conexion, "SELECT id_articulo FROM articulos WHERE LOWER(TRIM(titulo)) = '$titulo_normalizado'");
if (mysqli_num_rows($check_titulo) > 0) {
    die("❌ Error: Ya existe un artículo con ese título.");
}

// nombres distintos
$nombres_autores = array_filter(array_map(fn($a) => strtolower(trim($a["nombre"])), $autores));
if (count($nombres_autores) !== count(array_unique($nombres_autores))) {
    die("❌ Error: Autor repetido.");
}

// correos distintos
$correos_autores = array_filter(array_map(fn($a) => strtolower(trim($a["email"])), $autores));
if (count($correos_autores) !== count(array_unique($correos_autores))) {
    die("❌ Error: Correo repetido.");
}

// ver que el correo no exista
foreach ($correos_autores as $correo) {
    $correo_esc = mysqli_real_escape_string($conexion, $correo);
    $check_autor = mysqli_query($conexion, "SELECT 1 FROM autores WHERE correo_autor = '$correo_esc'");
    $check_revisor = mysqli_query($conexion, "SELECT 1 FROM revisores WHERE correo_revisor = '$correo_esc'");

    if (mysqli_num_rows($check_autor) > 0 || mysqli_num_rows($check_revisor) > 0) {
        die("❌ Error: El correo '$correo' ya está registrado en el sistema.");
    }
}

// agregar artículo
$titulo_esc = mysqli_real_escape_string($conexion, $titulo);
$resumen_esc = mysqli_real_escape_string($conexion, $resumen);
$query_articulo = "INSERT INTO articulos (titulo, resumen, fecha_envio, estado) VALUES ('$titulo_esc', '$resumen_esc', '$fecha_envio', '$estado')";
mysqli_query($conexion, $query_articulo);
$id_articulo = mysqli_insert_id($conexion);

// agregar tópicos 
$topicos_array = explode(",", $topicos_raw);
foreach ($topicos_array as $topico) {
    $id_topico = intval(trim($topico));
    if ($id_topico >= 1 && $id_topico <= 18) {
        $query_topico = "INSERT INTO topicos_articulos (id_articulo, id_especialidad_topico) VALUES ($id_articulo, $id_topico)";
        mysqli_query($conexion, $query_topico);
    }
}

// agregar autores
foreach ($autores as $index => $autor) {
    $nombre = trim($autor["nombre"]);
    $correo = trim($autor["email"]);

    if (empty($nombre) || empty($correo)) continue;

    $nombre_esc = mysqli_real_escape_string($conexion, $nombre);
    $correo_esc = mysqli_real_escape_string($conexion, $correo);

    $rut_autor = str_pad(rand(1, 999999999), 9, "0", STR_PAD_LEFT);
    $usuario_autor = strtolower(explode("@", $correo)[0]) . rand(100, 999);
    $contraseña_autor = password_hash("pan1", PASSWORD_DEFAULT);
    $rol_autor = ($index == $autor_contacto_index) ? 'autor_contacto' : 'autor';

    $query_autor = "INSERT INTO autores (rut_autor, nombre_autor, correo_autor, rol_autor, usuario_autor, contraseña_autor)
                    VALUES ('$rut_autor', '$nombre_esc', '$correo_esc', '$rol_autor', '$usuario_autor', '$contraseña_autor')";
    mysqli_query($conexion, $query_autor);
}

echo "✅ Artículo enviado correctamente.";
echo "<br><a href='home.php'>Volver al Panel del Autor</a>";
?>

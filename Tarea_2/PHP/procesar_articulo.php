<?php
session_start();
include("conexion.php");

if (!isset($_SESSION["usuario_rut"]) || $_SESSION["usuario_tipo"] !== "autor") {
    header("Location: login.php");
    exit();
}

// Obtener datos del formulario
$titulo = trim($_POST["titulo"]);
$resumen = trim($_POST["resumen"]);
$topicos_raw = $_POST["topicos"];
$autores = $_POST["autor"];
$autor_contacto_index = intval($_POST["autor_contacto_index"]);

// Validar título único
$titulo_normalizado = mysqli_real_escape_string($conexion, strtolower(trim($titulo)));
$check_titulo = mysqli_query($conexion, "SELECT id_articulo FROM Articulos WHERE LOWER(titulo) = '$titulo_normalizado'");
if (mysqli_num_rows($check_titulo) > 0) {
    die("❌ Error: Ya existe un artículo con ese título.");
}

// Iniciar transacción
mysqli_begin_transaction($conexion);

try {
    // 1. Insertar el artículo
    $query_articulo = "INSERT INTO Articulos (titulo, resumen, fecha_envio, estado) 
                      VALUES (?, ?, CURDATE(), 'enviado')";
    $stmt = mysqli_prepare($conexion, $query_articulo);
    mysqli_stmt_bind_param($stmt, "ss", $titulo, $resumen);
    mysqli_stmt_execute($stmt);
    $id_articulo = mysqli_insert_id($conexion);
    
    // 2. Insertar tópicos
    $topicos_array = array_map('intval', explode(",", $topicos_raw));
    foreach ($topicos_array as $id_topico) {
        if ($id_topico >= 1 && $id_topico <= 18) {
            mysqli_query($conexion, 
                "INSERT INTO Topicos_Articulos (id_articulo, id_especialidad_topico) 
                 VALUES ($id_articulo, $id_topico)");
        }
    }
    
    // 3. Procesar autores
    $autores_procesados = [];
    $contacto_data = []; // Almacenará datos del autor de contacto
    
    // Autor 1 (logueado)
    $rut_autor_logueado = $_SESSION["usuario_rut"];
    $sql_autor = mysqli_query($conexion, "SELECT * FROM Autores WHERE rut_autor = '$rut_autor_logueado'");
    $autor_logueado = mysqli_fetch_assoc($sql_autor);
    
    $autores_procesados[0] = [
        'rut' => $rut_autor_logueado,
        'nombre' => $autor_logueado['nombre_autor'],
        'email' => $autor_logueado['correo_autor'],
        'usuario' => $autor_logueado['usuario_autor'],
        'contraseña' => $autor_logueado['contraseña_autor']
    ];
    
    // Si el autor logueado es el de contacto, guardamos sus datos
    if ($autor_contacto_index === 0) {
        $contacto_data = [
            'email' => $autor_logueado['correo_autor'],
            'usuario' => $autor_logueado['usuario_autor'],
            'contraseña' => $autor_logueado['contraseña_autor']
        ];
    }
    
    // Autores adicionales (2 y 3)
    for ($i = 1; $i <= 2; $i++) {
        if (!empty($autores[$i]['nombre']) && !empty($autores[$i]['email'])) {
            $nombre = trim($autores[$i]['nombre']);
            $email = trim($autores[$i]['email']);
            
            // Verificar si el autor ya existe
            $check = mysqli_query($conexion, 
                "SELECT * FROM Autores WHERE correo_autor = '".mysqli_real_escape_string($conexion, $email)."'");
            
            if (mysqli_num_rows($check) > 0) {
                $autor_existente = mysqli_fetch_assoc($check);
                $rut = $autor_existente['rut_autor'];
                $usuario = $autor_existente['usuario_autor'];
                $contraseña = $autor_existente['contraseña_autor'];
            } else {
                // Crear nuevo autor
                $rut = generarRut();
                $usuario = strtolower(explode('@', $email)[0]);
                $contraseña = password_hash('temp123', PASSWORD_DEFAULT);
                
                mysqli_query($conexion,
                    "INSERT INTO Autores (rut_autor, nombre_autor, correo_autor, usuario_autor, contraseña_autor)
                     VALUES ('$rut', '".mysqli_real_escape_string($conexion, $nombre)."', 
                            '".mysqli_real_escape_string($conexion, $email)."', 
                            '$usuario', '$contraseña')");
            }
            
            $autores_procesados[$i] = [
                'rut' => $rut,
                'nombre' => $nombre,
                'email' => $email,
                'usuario' => $usuario,
                'contraseña' => $contraseña
            ];
            
            // Si este autor es el de contacto, guardamos sus datos
            if ($autor_contacto_index === $i) {
                $contacto_data = [
                    'email' => $email,
                    'usuario' => $usuario,
                    'contraseña' => $contraseña
                ];
            }
        }
    }
    
    // 4. Insertar en envio_articulo (usando los datos del contacto para todos los registros)
    foreach ($autores_procesados as $autor) {
        mysqli_query($conexion,
            "INSERT INTO Envio_Articulo (rut_autor, id_articulo, autor_contacto, usuario_contacto, contraseña_contacto)
             VALUES ('{$autor['rut']}', $id_articulo, 
                    '".mysqli_real_escape_string($conexion, $contacto_data['email'])."', 
                    '".mysqli_real_escape_string($conexion, $contacto_data['usuario'])."', 
                    '".mysqli_real_escape_string($conexion, $contacto_data['contraseña'])."')");
    }
    
    // Confirmar transacción
    mysqli_commit($conexion);

    // Redirigir a la página de éxito
    header("Location: publicacion_existosa.php");

    exit();

} catch (Exception $e) {
    // Revertir en caso de error
    mysqli_rollback($conexion);
    die("❌ Error: " . $e->getMessage());
}

// Función para generar RUT válido
function generarRut() {
    $numero = rand(1000000, 25000000);
    $s = 1;
    for ($m = 0; $numero != 0; $numero /= 10) {
        $s = ($s + $numero % 10 * (9 - $m++ % 6)) % 11;
    }
    $dv = chr($s ? $s + 47 : 75);
    return "$numero-$dv";
}

?>
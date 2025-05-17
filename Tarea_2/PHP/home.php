<?php
session_start();
if (!isset($_SESSION["usuario_rut"]) || $_SESSION["usuario_tipo"] !== "autor") {
    header("Location: login.php");
    exit();
}

include("../HTML/home.html");

echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            let errorMessage = document.getElementById('error-message');
            if (errorMessage) {
                errorMessage.textContent = '" . htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') . "';
            }
        });
      </script>";
?>

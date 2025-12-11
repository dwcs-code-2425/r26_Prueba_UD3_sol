<?php

session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

/* Crear cookie que dura 30 días */
setcookie("visto_publi", "1", time() + (30*24*60*60));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <title>Promoción especial MiniBank</title>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card p-4 shadow-sm">
        <h2>¡Nueva promoción MiniBank!</h2>
        <p>
            Contrata nuestro depósito al <strong>3% TAE</strong> a 3 meses.<br>
            Importe máximo: <strong>30.000 €</strong>.
        </p>

        <a href="listado.php" class="btn btn-primary">Continuar</a>
    </div>
</div>

</body>
</html>

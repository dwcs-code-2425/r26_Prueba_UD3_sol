<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/util.php';


$cuentas = [];
try {
    $cuentas = getCuentas($_SESSION['user_id']);
} catch (Exception $e) {
    error_log("Error al obtener cuentas: " . $e->getMessage());
    $_SESSION['msg'] .= "Error al cargar las cuentas.";
}

$msg = $_SESSION['msg'] ?? "";
unset($_SESSION['msg']);


?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <title>Cuentas – MiniBank</title>
</head>

<body class="bg-light">

    <div class="container mt-5">
        <h2 class="mb-4">Mis cuentas</h2>

       
        <?php mostrarMensaje($msg, "info"); ?>

        <div class="mb-3">
            <a href="transferir.php" class="btn btn-primary">Nueva transferencia</a>
        </div>

        <?php if (empty($cuentas)): ?>
            <p>No tienes cuentas asociadas.</p>
        <?php else: ?>
        <table class="table table-bordered table-striped">
            <tr>
                <th>ID</th>
                <th>Importe (€)</th>

            </tr>

            <?php foreach ($cuentas as $c): ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td><?= number_format($c['importe'], 2) ?></td>

                </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>

        <a href="logout.php" class="btn btn-danger">Cerrar sesión</a>
    </div>

</body>

</html>
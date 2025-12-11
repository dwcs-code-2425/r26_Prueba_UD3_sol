<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

require_once __DIR__ . '/conexion.php';
$pdo = getConnection();

$stmt = $pdo->prepare("SELECT * FROM cuentas WHERE titular_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$cuentas = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

    <?php if ($msg): ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>

    <div class="mb-3">
        <a href="transferir.php" class="btn btn-primary">Nueva transferencia</a>
    </div>

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

    <a href="logout.php" class="btn btn-danger">Cerrar sesión</a>
</div>

</body>
</html>

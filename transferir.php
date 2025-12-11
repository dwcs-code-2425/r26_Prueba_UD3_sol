<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/conexion.php';
$pdo = getConnection();

$msg = "";
$user_id = $_SESSION['user_id'];

/* ====== Cargar cuentas del usuario ====== */
$stmt = $pdo->prepare("SELECT * FROM cuentas WHERE titular_id=?");
$stmt->execute([$user_id]);
$cuentas = $stmt->fetchAll(PDO::FETCH_ASSOC);



/* ====== Procesar formulario ====== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $origen = $_POST['cuenta_origen'];
    $destino = $_POST['cuenta_destino'];
    $importe = floatval($_POST['importe']);

    /* Validación: cuenta origen del usuario */
    $stmt = $pdo->prepare("SELECT * FROM cuentas WHERE id=? AND titular_id=?");
    $stmt->execute([$origen, $user_id]);
    $cuenta_origen = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($origen == $destino) {
        $msg = "La cuenta origen y destino no pueden ser la misma";
    } elseif (!$cuenta_origen) {

        $msg = "La cuenta origen no pertenece al usuario";
    } else {
        /* Validar cuenta destino */


        $stmt = $pdo->prepare("SELECT * FROM cuentas WHERE id=?");
        $stmt->execute([$destino]);
        $cuenta_destino = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cuenta_destino) {
            $msg = "La cuenta destino no existe";
        } elseif ($cuenta_origen['importe'] < $importe) {
            $msg = "Saldo insuficiente";
        } elseif($importe <= 0){
            $msg = "El importe debe ser positivo";
        } else {
            /* Transacción */
            try {
                $pdo->beginTransaction();

                $stmt = $pdo->prepare("UPDATE cuentas SET importe = importe - ? WHERE id=?");
                $stmt->execute([$importe, $origen]);

                $stmt = $pdo->prepare("UPDATE cuentas SET importe = importe + ? WHERE id=?");
                $stmt->execute([$importe, $destino]);

                $pdo->commit();

                $_SESSION['msg'] = "Transferencia realizada correctamente";
                header("Location: listado.php");
                exit;

            } catch (Exception $e) {
                $pdo->rollback();
                $msg = "Error en la transferencia";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <title>Transferir – MiniBank</title>
</head>

<body class="bg-light">

    <div class="container mt-5">

        <h2 class="mb-4">Transferencia bancaria</h2>

        <?php if ($msg): ?>
            <div class="alert alert-danger"><?= $msg ?></div>
        <?php endif; ?>

        <form action="" method="POST" class="card p-4 shadow-sm">

            <div class="mb-3">
                <label class="form-label">Cuenta origen</label>
                <select name="cuenta_origen" class="form-select" required>
                    <?php foreach ($cuentas as $c): ?>
                        <option value="<?= $c['id'] ?>">
                            Cuenta <?= $c['id'] ?> — <?= number_format($c['importe'], 2) ?> €
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Cuenta destino (ID)</label>
                <input type="number" class="form-control" name="cuenta_destino" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Importe (€)</label>
                <input type="number" step="0.01" class="form-control" name="importe" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Transferir</button>
        </form>

        <a href="listado.php" class="btn btn-secondary mt-3">Volver</a>
    </div>

</body>

</html>
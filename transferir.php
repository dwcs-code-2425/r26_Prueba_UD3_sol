<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/util.php';


$msg = "";
$user_id = $_SESSION['user_id'];

/* ====== Cargar cuentas del usuario ====== */
$cuentas = [];
try {
    $cuentas = getCuentas($_SESSION['user_id']);
} catch (Exception $e) {
    error_log("Error al obtener cuentas: " . $e->getMessage());
    $msg .= "Error al cargar las cuentas.";
}



/* ====== Procesar formulario ====== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $origen = $_POST['cuenta_origen'];
    $destino = $_POST['cuenta_destino'];
    $importe = floatval($_POST['importe']);

    /* Validación: cuenta origen del usuario */
    // $stmt = $pdo->prepare("SELECT * FROM cuentas WHERE id=? AND titular_id=?");
    // $stmt->execute([$origen, $user_id]);
    // $cuenta_origen = $stmt->fetch(PDO::FETCH_ASSOC);

    //Lo más sencillo de validar en primer lugar:
    if ($origen == $destino) {
        $msg = "La cuenta origen y destino no pueden ser la misma";
    } elseif ($importe <= 0) {
        $msg = "El importe debe ser positivo";
    } else {

        $cuenta_origen = false;
        $cuenta_destino = false;
        //comprobamos que la cuenta de origen pertenece al usuario
        try {
            $cuenta_origen = getCuentaPorIdYUserId($origen, $user_id);
        } catch (Exception $e) {
            error_log("Error al obtener la cuenta origen: " . $e->getMessage());
            $msg = "Error al procesar la cuenta origen";
        }

        if (!$cuenta_origen) {
            $msg = "La cuenta origen no pertenece al usuario";

        } else {
            /* Validar cuenta destino */

            try {
                $cuenta_destino = getCuentaPorId($destino);
            } catch (Exception $e) {
                error_log("Error al obtener la cuenta destino: " . $e->getMessage());
                $msg = "Error al procesar la cuenta destino";
                // $stmt = $pdo->prepare("SELECT * FROM cuentas WHERE id=?");
                // $stmt->execute([$destino]);
                // $cuenta_destino = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            if (!$cuenta_destino) {
                $msg = "La cuenta destino no existe";

            } else {
                /* Transacción */

                if (transferir($origen, $destino, $importe)) {
                    $_SESSION['msg'] = "Transferencia realizada correctamente";
                    header("Location: listado.php");
                    exit;
                } else {
                    $_SESSION["msg"] = "Error al realizar la transferencia";
                }
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


        <?php mostrarMensaje($msg, "danger"); ?>

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
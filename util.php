<?php

function getCuentas(int $user_id): array
{
    $cuentas = [];
    try {
        $pdo = getConnection();

        $stmt = $pdo->prepare("SELECT * FROM cuentas WHERE titular_id = ?");
        $stmt->execute([$user_id]);
        $cuentas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } finally {
        $pdo = null;
        $stmt = null;
    }
    return $cuentas;
}

function mostrarMensaje(string $msg, string $tipo): void
{
    if ($msg) {
        echo "<div class='alert alert-$tipo'>" . htmlspecialchars($msg) . '</div>';
    }

}

function getCuentaPorId(int $cuenta_id): array|false
{
    $cuenta = false;

    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT * FROM cuentas WHERE id=?");
        $stmt->execute([$cuenta_id]);
        $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);
        return $cuenta;
    } finally {
        $pdo = null;
        $stmt = null;
    }
}

function getCuentaPorIdYUserId(int $cuenta_id, int $user_id): array|false
{
    $cuenta = false;

    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT * FROM cuentas WHERE id=? AND titular_id=?");
        $stmt->execute([$cuenta_id, $user_id]);
        $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);
        return $cuenta;
    } finally {
        $pdo = null;
        $stmt = null;
    }
}

function transferir($cuenta_origen_id, $cuenta_destino_id, $importe): bool
{
    try {
        $pdo = getConnection();
        $pdo->beginTransaction();

        $stmt1 = $pdo->prepare("UPDATE cuentas SET importe = importe - ? WHERE id = ?");
        $stmt1->execute([$importe, $cuenta_origen_id]);

        $stmt2 = $pdo->prepare("UPDATE cuentas SET importe = importe + ? WHERE id = ?");
        $stmt2->execute([$importe, $cuenta_destino_id]);

        return $pdo->commit();
      
    } catch (Exception $e) {
        if ($pdo) {
            $pdo->rollBack();
        }
        error_log("Error en la transferencia: " . $e->getMessage());
        return false;
    } finally {
        $pdo = null;
        $stmt1 = null;
        $stmt2 = null;
    }
    
}

function getUserByEmail(string $email): array|false{
    $user = false;
    try {
        $pdo = getConnection();

        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user;
    }
    finally {
        $pdo = null;
        $stmt = null;
    }
}
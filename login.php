<?php
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/util.php';

$msg = "";

/* ========= PROCESAMIENTO DEL FORMULARIO ========= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'] ?? "";
    $password = $_POST['password'] ?? "";

    try {
        // $pdo = getConnection();

        // $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        // $stmt->execute([$email]);
        // $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $user = getUserByEmail($email);
    


        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['msg'] = "Autenticación correcta";


            if (!isset($_COOKIE['visto_publi'])) {
                header("Location: publi.php");
                exit;
            }


            header("Location: listado.php");
            exit;
        } else {
            $msg = "Error en email o contraseña";
        }

    } catch (Exception $e) {
        error_log("Error en la autenticación: " . $e->getMessage());
        $msg = "Error en la autenticación";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <title>Acceso a MiniBank</title>
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">

                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="text-center mb-4">MiniBank</h3>

                    
                        <?php mostrarMensaje($msg, "alert");?>

                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Contraseña</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>

                            <button class="btn btn-primary w-100" type="submit">Acceder</button>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

</body>

</html>
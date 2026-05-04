<?php
session_start();
if(isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
$msg = ''; $type = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_encode(['username' => $_POST['username'], 'password' => $_POST['password']]);
    $options =[
        'http' =>['header' => "Content-type: application/json\r\n", 'method' => 'POST', 'content' => $data],
        'ssl' =>['verify_peer' => false, 'verify_peer_name' => false]
    ];
    $context  = stream_context_create($options);
    $result = @file_get_contents('http://api-gateway:8080/api/users/register', false, $context);
    if($result) {
        $res = json_decode($result, true);
        $msg = $res['message'];
        $type = $res['status'] === 'OK' ? 'success' : 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registre - ShopMicro</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="styles/register.css" rel="stylesheet">
</head>
<body>
<div class="register-container">
    <div class="register-card">
        <div class="register-header">
            <i class="fas fa-user-plus register-icon"></i>
            <h2>ShopMicro</h2>
            <p>Crea el teu compte</p>
        </div>
        <div class="register-body">
            <?php if($msg): ?>
                <div class="alert-message <?= $type === 'success' ? 'alert-success' : 'alert-danger' ?>">
                    <i class="fas <?= $type === 'success' ? 'fa-check-circle' : 'fa-circle-xmark' ?>"></i><?= $msg ?>
                </div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group input-icon username">
                    <input type="text" name="username" class="form-control" placeholder="Nou nom d'usuari" required>
                </div>
                <div class="form-group input-icon password">
                    <input type="password" name="password" class="form-control" placeholder="Crea una contrasenya" required>
                </div>
                <button type="submit" class="btn-register">Crear Compte</button>
            </form>
        </div>
        <div class="register-footer">
            <p>Ja tens compte? <a href="login.php">Inicia sessió aquí</a></p>
        </div>
    </div>
</div>
</body>
</html>

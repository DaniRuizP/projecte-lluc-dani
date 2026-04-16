<?php
session_start();
if(isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
$error = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_encode(['username' => $_POST['username'], 'password' => $_POST['password']]);
    $options =[
        'http' =>['header' => "Content-type: application/json\r\n", 'method' => 'POST', 'content' => $data],
        'ssl' =>['verify_peer' => false, 'verify_peer_name' => false]
    ];
    $context  = stream_context_create($options);
    $result = @file_get_contents('http://api-gateway:8080/api/users/login', false, $context);
    if($result) {
        $res = json_decode($result, true);
        if($res['status'] === 'OK') {
            $_SESSION['user_id'] = $res['user']['id'];
            $_SESSION['username'] = $res['user']['username'];
            header('Location: index.php'); exit;
        } else { $error = $res['message']; }
    } else { $error = "Error connectant amb l'API de Login."; }
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ShopMicro</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="styles/login.css" rel="stylesheet">
</head>
<body>
<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-lock login-icon"></i>
            <h2>ShopMicro</h2>
            <p>Inicia sessió al teu compte</p>
        </div>
        <div class="login-body">
            <?php if($error): ?>
                <div class="alert-danger">
                    <i class="fas fa-circle-xmark"></i><?= $error ?>
                </div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group input-icon username">
                    <input type="text" name="username" class="form-control" placeholder="Nom d'usuari" required>
                </div>
                <div class="form-group input-icon password">
                    <input type="password" name="password" class="form-control" placeholder="Contrasenya" required>
                </div>
                <button type="submit" class="btn-login">Entrar</button>
            </form>
        </div>
        <div class="login-footer">
            <p>No tens compte? <a href="register.php">Registra't aquí</a></p>
        </div>
    </div>
</div>
</body>
</html>

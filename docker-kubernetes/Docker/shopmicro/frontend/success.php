<?php
session_start();
// Protecció: Si no està loguejat, fora.
if(!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

// Agafem el total pagat de la URL (si no hi és, posem 0.00)
$total = isset($_GET['total']) ? htmlspecialchars($_GET['total']) : '0.00';
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✅ Compra Realitzada - ShopMicro</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="styles/success.css" rel="stylesheet">
</head>
<body>
<div class="success-container">
    <div class="success-card">
        <div class="success-header">
            <i class="fas fa-check-circle success-icon"></i>
            <h2>Compra Completada!</h2>
            <p>ShopMicro</p>
        </div>
        <div class="success-body">
            <p>Moltes gràcies, <b><?= htmlspecialchars($_SESSION['username']) ?></b>.<br>La teva comanda s'ha processat correctament i l'estoc ha estat actualitzat a la base de dades.</p>
            
            <div class="total-amount">
                Total pagat: <?= $total ?> €
            </div>
            
            <p class="notification-info"><i class="fas fa-bell"></i> S'ha enviat una notificació a la cua de <b>RabbitMQ</b>.</p>
        </div>
        <div class="success-footer">
            <a href="index.php" class="btn-success"><i class="fas fa-arrow-left"></i> Tornar a la botiga</a>
        </div>
    </div>
</div>
</body>
</html>
<?php
session_start();
require_once __DIR__ . '/../includes/conexao.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REVAMP🌐-GALLERY</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=BBH+Bartle&family=Saira:ital,wght@0,100..900;1,100..900&family=Source+Code+Pro:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header-container">
        <nav>
            <a href="../index.php">SHOP</a>
            <a href="gallery.php">PHOTOS</a>
            <a href="sac.php">SAC</a>
            <div class="nav-right-icons">
                <a href="../bag.php"><i class="fas fa-shopping-bag"></i></a>
                 <?php if (isset($_SESSION['usuario_id'])): ?>
                    <a href="profile/minha_conta.php"><i class="fas fa-user"></i></a>
                <?php else: ?>
                    <a href="../login.php"><i class="fas fa-user"></i></a>
                <?php endif; ?>
                </div>
        </nav>
    </header>
</body>
</html>
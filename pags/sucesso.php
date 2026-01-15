<?php
session_start();
require_once __DIR__ . '/../includes/conexao.php';

// Se não tem pedido recente, redireciona
if (!isset($_SESSION['ultimo_pedido_id'])) {
    header("Location: ../index.php");
    exit;
}

// Busca dados do pedido
$stmt = $conn->prepare("SELECT * FROM pedidos WHERE id = :id");
$stmt->execute([':id' => $_SESSION['ultimo_pedido_id']]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    header("Location: ../index.php");
    exit;
}

// Busca itens do pedido
$stmt = $conn->prepare("SELECT * FROM pedido_itens WHERE pedido_id = :pedido_id");
$stmt->execute([':pedido_id' => $pedido['id']]);
$itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PEDIDO CONFIRMADO - REVAMP🌐</title>
    
    <link rel="stylesheet" href="../assets/css/styles.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Saira:wght@300;400;700&family=Source+Code+Pro:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="top-announcement-bar">
        <div class="ticker-content">
            <span>SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972</span>
            <span>SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972</span>
        </div>
    </div>

    <header class="header-container">
        <nav>
            <a href="../index.php">SHOP</a>
            <a href="gallery.php">PHOTOS</a>
            <a href="sac.php">SAC</a>
            <div class="nav-right-icons">
                <a href="bag.php"><i class="fas fa-shopping-bag"></i></a>
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <a href="profile/minha_conta.php"><i class="fas fa-user"></i></a>
                <?php else: ?>
                    <a href="../login.php"><i class="fas fa-user"></i></a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="success-wrapper">
        <div class="success-container">
            <div class="success-content">
                <i class="fas fa-check-circle success-icon"></i>
                
                <h1>PEDIDO CONFIRMADO!</h1>
                <p class="order-number">Pedido #<?php echo str_pad($pedido['id'], 6, '0', STR_PAD_LEFT); ?></p>
                
                <div class="success-message">
                    <p>Obrigado pela sua compra! Você receberá um email de confirmação com os detalhes do pedido.</p>
                    <p>Rastreamento disponível em breve.</p>
                </div>

                <div class="order-details">
                    <h2>RESUMO DO PEDIDO</h2>
                    
                    <div class="detail-row">
                        <span>Data do Pedido:</span>
                        <strong><?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></strong>
                    </div>

                    <div class="detail-row">
                        <span>Status:</span>
                        <strong class="status-badge"><?php echo ucfirst($pedido['status']); ?></strong>
                    </div>

                    <div class="detail-row">
                        <span>Método de Pagamento:</span>
                        <strong><?php echo strtoupper($pedido['metodo_pagamento']); ?></strong>
                    </div>

                    <div class="detail-row">
                        <span>Total:</span>
                        <strong>R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></strong>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="../index.php" class="btn-primary">CONTINUAR COMPRANDO</a>
                    <a href="profile/minha_conta.php" class="btn-secondary">MEUS PEDIDOS</a>
                </div>
            </div>
        </div>
    </main>

    <style>
        .success-wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 60px 20px;
            background-color: #000;
        }

        .success-container {
            width: 100%;
            max-width: 500px;
            text-align: center;
            color: #fff;
        }

        .success-icon {
            font-size: 5rem;
            color: #00d084;
            display: block;
            margin-bottom: 30px;
            animation: pulse 1s ease-in-out;
        }

        @keyframes pulse {
            0% { transform: scale(0.8); opacity: 0; }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }

        .success-container h1 {
            font-family: 'Saira', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: 3px;
            margin-bottom: 10px;
        }

        .order-number {
            font-family: 'Source Code Pro', monospace;
            color: #959595;
            font-size: 0.9rem;
            margin-bottom: 30px;
            letter-spacing: 2px;
        }

        .success-message {
            background: #0a0a0a;
            padding: 30px;
            border: 1px solid #1a1a1a;
            margin-bottom: 40px;
            border-radius: 4px;
        }

        .success-message p {
            font-size: 0.95rem;
            line-height: 1.6;
            color: #ccc;
            margin-bottom: 10px;
        }

        .success-message p:last-child {
            margin-bottom: 0;
        }

        .order-details {
            text-align: left;
            background: #0a0a0a;
            padding: 30px;
            border: 1px solid #1a1a1a;
            margin-bottom: 40px;
            border-radius: 4px;
        }

        .order-details h2 {
            font-family: 'Saira', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 2px;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #1a1a1a;
            font-size: 0.9rem;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-row span {
            color: #959595;
        }

        .detail-row strong {
            color: #fff;
        }

        .status-badge {
            background: #00d084;
            color: #000;
            padding: 4px 12px;
            border-radius: 3px;
            font-size: 0.85rem;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            flex-direction: column;
        }

        .btn-primary, .btn-secondary {
            padding: 16px 30px;
            text-decoration: none;
            font-family: 'Saira', sans-serif;
            font-weight: 700;
            font-size: 0.9rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            border-radius: 4px;
            transition: 0.3s;
            display: block;
        }

        .btn-primary {
            background: #fff;
            color: #000;
        }

        .btn-primary:hover {
            background: #ccc;
        }

        .btn-secondary {
            border: 1px solid #fff;
            color: #fff;
        }

        .btn-secondary:hover {
            background: #fff;
            color: #000;
        }
    </style>
</body>
</html>

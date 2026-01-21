<?php
session_start();
require_once __DIR__ . '/includes/conexao.php';

if (isset($_GET['remover'])) {
    $chave_remover = $_GET['remover'];
    if (isset($_SESSION['carrinho'][$chave_remover])) {
        unset($_SESSION['carrinho'][$chave_remover]);
    }
    header("Location: bag.php");
    exit;
}

$itens_carrinho_detalhes = [];
$total_geral = 0;

if (!empty($_SESSION['carrinho'])) {
    foreach ($_SESSION['carrinho'] as $chave => $item) {
        
        $stmt = $conn->prepare("SELECT * FROM produtos WHERE id = :id");
        $stmt->execute([':id' => $item['id']]);
        $produto_banco = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($produto_banco) {
            $subtotal = $produto_banco['preco'] * $item['quantidade'];
            $total_geral += $subtotal;
            
            
            $itens_carrinho_detalhes[] = [
                'chave'      => $chave,
                'nome'       => $produto_banco['nome'],
                'preco'      => $produto_banco['preco'],
                'imagem'     => $produto_banco['imagem'],
                'tamanho'    => $item['tamanho'],
                'quantidade' => $item['quantidade'],
                'subtotal'   => $subtotal
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BAG 🌐</title>
    <link rel="stylesheet" href="/REVAMP/assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Saira:wght@100..900&family=Source+Code+Pro:wght@200..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="top-announcement-bar">
        <div class="ticker-content">
            <span>MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐  </span>
            <span>MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐 </span>
        </div>
    </div>
    
    <header class="header-container">
        <nav>
            <a href="/REVAMP/index.php">SHOP</a>
            <a href="/REVAMP/gallery.php">PHOTOS</a>
            <a href="/REVAMP/sac.php">SAC</a>
            <div class="nav-right-icons">
                <a href="/REVAMP/bag.php"><i class="fas fa-shopping-bag"></i></a>
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <a href="/REVAMP/pags/profile/minha_conta.php"><i class="fas fa-user"></i></a>
                <?php else: ?>
                    <a href="/REVAMP/login.php"><i class="fas fa-user"></i></a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="cart-container">
        <h1>SUA SACOLA</h1>

        <?php if (empty($itens_carrinho_detalhes)): ?>
            <div>
                <p>Sua sacola está vazia no momento.</p>
                <br><br>
                <a href="../index.php" class="checkout-btn">VOLTAR PARA O SHOP</a>
            </div>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Preço</th>
                        <th>Quantidade</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($itens_carrinho_detalhes as $item): ?>
                    <tr>
                        <td data-label="Produto">
                            <div class="product-cart-item">
                                <img src="/REVAMP/assets/img/produtos/<?php echo $item['imagem']; ?>" class="img-cart" alt="<?php echo $item['nome']; ?>">
                                <div class="product-cart-info">
                                    <strong><?php echo htmlspecialchars($item['nome']); ?></strong>
                                    <small>TAMANHO: <?php echo $item['tamanho']; ?></small>
                                </div>
                            </div>
                        </td>
                        <td data-label="Preço">
                            R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?>
                        </td>
                        <td data-label="Qtd">
                            <?php echo $item['quantidade']; ?>
                        </td>
                        <td data-label="Subtotal">
                            <strong>R$ <?php echo number_format($item['subtotal'], 2, ',', '.'); ?></strong>
                        </td>
                        <td>
                            <a href="bag.php?remover=<?php echo $item['chave']; ?>" class="remove-btn" title="Remover item">
                                <i class="fas fa-trash-can"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-footer">
                <div class="cart-total-value">
                    <span>TOTAL: R$ <?php echo number_format($total_geral, 2, ',', '.'); ?></span>
                </div>
                
                <div class="checkout-actions">
                    <a href="/REVAMP/index.php" class="btn-continue">Continuar Comprando</a>
                        <a href="/REVAMP/checkout.php" class="checkout-btn">FINALIZAR COMPRA</a>
                </div>
            </div>
        <?php endif; ?>
    </main>

     <footer class="footer">
    <p>&copy; 2025 REVAMP. Todos os direitos reservados.</p>
    <div class="footer-links">
        <a href="sac.php">SAC</a>
    </div>
</body>
</html>
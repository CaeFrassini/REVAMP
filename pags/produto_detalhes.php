<?php
session_start();
require_once __DIR__ . '/../includes/conexao.php';

// Pega o ID da URL
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: ../index.php");
    exit;
}

// Busca o produto no banco
$stmt = $conn->prepare("SELECT * FROM produtos WHERE id = :id");
$stmt->execute([':id' => $id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    header("Location: ../index.php?msg=produto_nao_encontrado");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?php echo $produto['nome']; ?>🌐</title>
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

    <main class="product-detail-wrapper">
        <div class="product-container">
            <div class="product-view">
                <?php
                    $imgWeb = '../assets/img/produtos/' . $produto['imagem'];
                    if (!empty($produto['imagem']) && file_exists(__DIR__ . '/../assets/img/produtos/' . $produto['imagem'])) {
                        echo '<img src="' . $imgWeb . '" alt="' . htmlspecialchars($produto['nome']) . '">';
                    } else {
                        echo '<div class="no-image">Sem imagem</div>';
                    }
                ?>
            </div>

            <div class="product-info-column">
                <h1 class="detail-name"><?php echo mb_strtoupper($produto['nome']); ?></h1>
                <p class="detail-price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                
                <div class="detail-description">
                    <p><?php echo nl2br($produto['descricao']); ?></p>
                </div>

                <form action="../includes/carrinho_adicionar.php" method="POST" class="purchase-form">
                    <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">
                
                    
                    <div class="size-selector">
                        <span>TAMANHO</span>
                        <div class="size-options">
                            <?php 
                            $grades = [
                                'P' => $produto['estoque_p'],
                                'M' => $produto['estoque_m'],
                                'G' => $produto['estoque_g'],
                                'GG' => $produto['estoque_gg']
                            ];

                            foreach ($grades as $tam => $qtd): 
                                $disponivel = $qtd > 0;
                            ?>
                                <label class="size-btn <?php echo !$disponivel ? 'disabled' : ''; ?>">
                                    <input type="radio" name="tamanho" value="<?php echo $tam; ?>" <?php echo !$disponivel ? 'disabled' : 'required'; ?>>
                                    <span class="size-name"><?php echo $tam; ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" class="add-to-cart-btn" <?php echo (array_sum($grades) <= 0) ? 'disabled' : ''; ?>>
                        <?php echo (array_sum($grades) <= 0) ? 'FORA DO ESTOQUE' : 'ADICIONAR NO CARRINHO'; ?>
                    </button>
                </form>

                <div class="extra-info">
                    <p><i class="fas fa-truck-fast"></i> ENVIO PARA TODO O BRASIL</p>
                    <p><i class="fas fa-shield-halved"></i> PAGAMENTO SEGURO</p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
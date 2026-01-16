<?php
session_start();
require_once __DIR__ . '/includes/conexao.php';

$query = "SELECT * FROM produtos";
$stmt = $conn->query($query);
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REVAMP 🌐</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/usuario.css">
    <link href="https://fonts.googleapis.com/css2?family=BBH+Bartle&family=Saira:ital,wght@0,100..900;1,100..900&family=Source+Code+Pro:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet">
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
            <a href="index.php">SHOP</a>
            <a href="pags/gallery.php">PHOTOS</a>
            <a href="pags/sac.php">SAC</a>
            <div class="nav-right-icons">
                <a href="pags/bag.php"><i class="fas fa-shopping-bag"></i></a>
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <a href="pags/profile/minha_conta.php"><i class="fas fa-user"></i></a>
                <?php else: ?>
                    <a href="login.php"><i class="fas fa-user"></i></a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
<section class="products-section">
    <div class="product-grid">
        <?php 
        // Lista de produtos obtida via PDO
        if(count($produtos) > 0):
            foreach($produtos as $produto):
        ?>
            <article class="product-card">
                <a href="pags/produto_detalhes.php?id=<?php echo $produto['id']; ?>">
                    <div class="product-image">
                            <?php
                                $imgWeb = '/REVAMP/assets/img/produtos/' . $produto['imagem'];
                                $imgFs = __DIR__ . '/assets/img/produtos/' . $produto['imagem'];
                                if (!empty($produto['imagem']) && file_exists($imgFs)) {
                                    echo '<img src="' . $imgWeb . '" alt="' . htmlspecialchars($produto['nome']) . '">';
                                } else {
                                    echo '<div class="no-image">Sem imagem</div>';
                                }
                            ?>
                        <div class="product-overlay">
                            <span>DETALHES</span>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name"><?php echo $produto['nome']; ?></h3>
                        <p class="product-price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                    </div>
                </a>
            </article>
        <?php 
            endforeach; 
        else:
            echo "<p>Nenhum produto encontrado.</p>";
        endif;
        ?>
    </div>
</section>
</body>
</html>
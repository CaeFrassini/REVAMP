<?php
session_start();
require_once __DIR__ . '/../includes/conexao.php';

// Proteção básica
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php?redirect=pags/checkout.php");
    exit;
}

if (empty($_SESSION['carrinho'])) {
    header("Location: bag.php");
    exit;
}

// Busca dados do usuário
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = :id");
$stmt->execute([':id' => $_SESSION['usuario_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$total_geral = 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHECKOUT 🌐</title>
    
    <link rel="stylesheet" href="../assets/css/styles.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Saira:wght@300;400;700&family=Source+Code+Pro:wght@300;400;600&display=swap" rel="stylesheet">
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
    <main class="checkout-wrapper">
        <div class="checkout-container">
            
            <form action="../includes/processar_pedido.php" method="POST">
                <h1 class="checkout-title">FINALIZAR PEDIDO</h1>

                <section class="checkout-section">
                    <div class="section-header">
                        <i class="fas fa-map-marker-alt"></i>
                        <h2>Endereço de Entrega</h2>
                    </div>
                    
                    <div class="input-row">
                        <div class="input-group">
                            <label>CEP</label>
                            <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '')" name="cep" placeholder="00000-000" value="<?php echo $usuario['cep'] ?? ''; ?>" required>
                        </div>
                    </div>

                    <div class="input-row grid-3-1">
                        <div class="input-group">
                            <label>Endereço</label>
                            <input type="text" name="rua" placeholder="Rua, Av..." required>
                        </div>
                        <div class="input-group">
                            <label>Número</label>
                            <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '')" pattern="\d*" name="numero" placeholder="123" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Complemento (Opcional)</label>
                        <input type="text" name="complemento" placeholder="Apto, Bloco...">
                    </div>
                </section>

                <section class="checkout-section">
                    <div class="section-header">
                        <i class="fas fa-credit-card"></i>
                        <h2>Pagamento</h2>
                    </div>
                    <div class="payment-options">
                        <label class="payment-card">
                            <input type="radio" name="metodo_pagamento" value="pix" checked>
                            <span class="radio-custom"></span>
                            <div class="payment-info">
                                <strong>PIX</strong>
                                <small>Aprovação imediata</small>
                            </div>
                            <i class="fa-brands fa-pix"></i>
                        </label>

                        <label class="payment-card">
                            <input type="radio" name="metodo_pagamento" value="cartao">
                            <span class="radio-custom"></span>
                            <div class="payment-info">
                                <strong>Cartão de Crédito</strong>
                            </div>
                            <i class="fas fa-credit-card"></i>
                        </label>
                    </div>
                </section>

                <button type="submit" class="btn-complete">CONCLUIR COMPRA</button>
            </form>

            <aside class="order-sidebar">
                <div class="sticky-sidebar">
                    <h3>RESUMO DO PEDIDO</h3>
                    <div class="order-items">
                        <?php 
                        foreach ($_SESSION['carrinho'] as $item): 
                            $st = $conn->prepare("SELECT nome, preco FROM produtos WHERE id = :id");
                            $st->execute([':id' => $item['id']]);
                            $p = $st->fetch(PDO::FETCH_ASSOC);
                            $subtotal = $p['preco'] * $item['quantidade'];
                            $total_geral += $subtotal;
                        ?>
                        <div class="item-line">
                            <div class="item-info">
                                <strong><?php echo $p['nome']; ?></strong>
                                <span>Tam: <?php echo $item['tamanho']; ?> | Qtd: <?php echo $item['quantidade']; ?></span>
                            </div>
                            <span class="item-price">R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="order-totals">
                        <div class="total-line">
                            <span>Subtotal</span>
                            <span>R$ <?php echo number_format($total_geral, 2, ',', '.'); ?></span>
                        </div>
                        <div class="total-line">
                            <span>Frete</span>
                            <span class="free-shipping">GRÁTIS</span>
                        </div>
                        <div class="total-line grand-total">
                            <span>TOTAL</span>
                            <span>R$ <?php echo number_format($total_geral, 2, ',', '.'); ?></span>
                        </div>
                    </div>
                </div>
            </aside>

        </div>
    </main>

</body>
<script>
document.querySelector('input[name="cep"]').addEventListener('blur', function() {
    // Remove caracteres não numéricos
    let cep = this.value.replace(/\D/g, '');

    // Verifica se o CEP possui 8 dígitos
    if (cep.length === 8) {
        // Mostra um aviso de "Carregando..." nos campos (opcional)
        const campoRua = document.querySelector('input[name="rua"]');
        campoRua.value = "...";

        // Faz a requisição à API ViaCEP
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                if (!data.erro) {
                    // Preenche os campos com os dados retornados
                    campoRua.value = data.logradouro;
                    
                    // Se você tiver campos de Bairro e Cidade, pode preencher assim:
                    // document.querySelector('input[name="bairro"]').value = data.bairro;
                    // document.querySelector('input[name="cidade"]').value = data.localidade;

                    // Coloca o foco no campo Número para o usuário continuar
                    document.querySelector('input[name="numero"]').focus();
                } else {
                    alert("CEP não encontrado.");
                    campoRua.value = "";
                }
            })
            .catch(error => {
                console.error('Erro ao buscar CEP:', error);
                campoRua.value = "";
            });
    }
});
</script>
</body>
</html>
</scrip
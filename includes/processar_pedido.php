<?php
session_start();
require_once __DIR__ . '/conexao.php';

// 1. Verificações de segurança: se não está logado ou carrinho está vazio
if (!isset($_SESSION['usuario_id']) || empty($_SESSION['carrinho'])) {
    header("Location: ../index.php");
    exit;
}

// 2. Receber e limpar os dados do formulário
$usuario_id = $_SESSION['usuario_id'];
$metodo_pagamento = $_POST['metodo_pagamento'] ?? 'pix';
$cep = $_POST['cep'] ?? '';
$endereco_completo = $_POST['rua'] . ", nº " . $_POST['numero'] . " " . ($_POST['complemento'] ?? '');

try {
    // Inicia uma Transação: se algo der errado em qualquer insert, nada é salvo
    $conn->beginTransaction();

    // 3. Calcular o Total do Pedido (sempre calcule no servidor por segurança)
    $total_pedido = 0;
    $itens_para_salvar = [];

    foreach ($_SESSION['carrinho'] as $item) {
        $stmt = $conn->prepare("SELECT preco, nome FROM produtos WHERE id = :id");
        $stmt->execute([':id' => $item['id']]);
        $produto_bd = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($produto_bd) {
            $subtotal = $produto_bd['preco'] * $item['quantidade'];
            $total_pedido += $subtotal;
            
            // Guardamos os dados para o segundo INSERT
            $itens_para_salvar[] = [
                'produto_id' => $item['id'],
                'tamanho' => $item['tamanho'],
                'quantidade' => $item['quantidade'],
                'preco_unitario' => $produto_bd['preco']
            ];
        }
    }

    // 4. Inserir na tabela 'pedidos'
    $sql_pedido = "INSERT INTO pedidos (usuario_id, total, endereco, cep, metodo_pagamento, status) 
                   VALUES (:uid, :total, :end, :cep, :pag, 'pendente')";
    
    $stmt = $conn->prepare($sql_pedido);
    $stmt->execute([
        ':uid'   => $usuario_id,
        ':total' => $total_pedido,
        ':end'   => $endereco_completo,
        ':cep'   => $cep,
        ':pag'   => $metodo_pagamento
    ]);

    // Pega o ID do pedido que acabou de ser criado
    $pedido_id = $conn->lastInsertId();

    // 5. Inserir os itens na tabela 'pedido_itens'
    $sql_itens = "INSERT INTO pedido_itens (pedido_id, produto_id, tamanho, quantidade, preco_unitario) 
                  VALUES (:pid, :prodid, :tam, :qtd, :preco)";
    
    $stmt_itens = $conn->prepare($sql_itens);

    foreach ($itens_para_salvar as $item) {
        $stmt_itens->execute([
            ':pid'    => $pedido_id,
            ':prodid' => $item['produto_id'],
            ':tam'    => $item['tamanho'],
            ':qtd'    => $item['quantidade'],
            ':preco'  => $item['preco_unitario']
        ]);
    }

    // 6. Tudo certo! Confirmar gravação no banco
    $conn->commit();

    // 7. Limpar o Carrinho (Sessão e Cookie)
    unset($_SESSION['carrinho']);
    if (isset($_COOKIE['revamp_cart'])) {
        setcookie('revamp_cart', '', time() - 3600, "/");
    }

    // 8. Redirecionar para a página de sucesso
    header("Location: ../pags/sucesso.php?id=" . $pedido_id);
    exit;

} catch (Exception $e) {
    // Se der erro, cancela tudo que foi feito no banco
    $conn->rollBack();
    die("Erro ao processar o pedido: " . $e->getMessage());
}
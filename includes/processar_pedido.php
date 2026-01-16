<?php
session_start();
require_once __DIR__ . '/conexao.php';

// 1. Verificações de segurança
if (!isset($_SESSION['usuario_id']) || empty($_SESSION['carrinho'])) {
    header("Location: ../index.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$metodo_pagamento = $_POST['metodo_pagamento'] ?? 'pix';
$cep = $_POST['cep'] ?? '';
$rua = $_POST['rua'] ?? '';
$numero = $_POST['numero'] ?? '';
$complemento = $_POST['complemento'] ?? '';
$endereco_completo = $rua . ", nº " . $numero . " " . $complemento;

try {
    // Inicia a transação para garantir integridade dos dados
    $conn->beginTransaction();

    // 2. Calcular o Total do Pedido e preparar os dados
    $total_pedido = 0;
    $itens_detalhados = [];

    foreach ($_SESSION['carrinho'] as $item) {
        $stmt_prod = $conn->prepare("SELECT preco FROM produtos WHERE id = :id");
        $stmt_prod->execute([':id' => $item['id']]);
        $res = $stmt_prod->fetch(PDO::FETCH_ASSOC);

        if ($res) {
            $preco_atual = $res['preco'];
            $subtotal = $preco_atual * $item['quantidade'];
            $total_pedido += $subtotal;

            $itens_detalhados[] = [
                'id' => $item['id'],
                'tam' => $item['tamanho'],
                'qtd' => $item['quantidade'],
                'preco' => $preco_atual
            ];
        }
    }

    // 3. Inserir na tabela 'pedidos'
    $sql_ped = "INSERT INTO pedidos (usuario_id, total, endereco, cep, metodo_pagamento, status) 
                VALUES (:uid, :tot, :end, :cep, :pag, 'pendente')";
    
    $stmt_ped = $conn->prepare($sql_ped);
    $stmt_ped->execute([
        ':uid'   => $usuario_id,
        ':tot'   => $total_pedido,
        ':end'   => $endereco_completo,
        ':cep'   => $cep,
        ':pag'   => $metodo_pagamento
    ]);

    $pedido_id = $conn->lastInsertId();

    // 4. Inserir itens e atualizar estoque
    $sql_item = "INSERT INTO pedido_itens (pedido_id, produto_id, tamanho, quantidade, preco_unitario) 
                 VALUES (:pid, :prodid, :tam, :qtd, :prc)";
    $stmt_item = $conn->prepare($sql_item);

    foreach ($itens_detalhados as $i) {
        // Insere o item do pedido
        $stmt_item->execute([
            ':pid'    => $pedido_id,
            ':prodid' => $i['id'],
            ':tam'    => $i['tam'],
            ':qtd'    => $i['qtd'],
            ':prc'    => $i['preco']
        ]);

        // ATUALIZAÇÃO DE ESTOQUE
        $coluna = "estoque_" . strtolower($i['tam']);
        $colunas_permitidas = ['estoque_p', 'estoque_m', 'estoque_g', 'estoque_gg'];

        if (in_array($coluna, $colunas_permitidas)) {
            // Placeholder :qtd_estoque e :id_prod devem bater com o array do execute
            $sql_stk = "UPDATE produtos SET $coluna = $coluna - :qtd_estoque WHERE id = :id_prod";
            $stmt_stk = $conn->prepare($sql_stk);
            $stmt_stk->execute([
                ':qtd_estoque' => $i['qtd'],
                ':id_prod'     => $i['id']
            ]);
        }
    }

    // 5. Finaliza a transação
    $conn->commit();

    // 6. Limpa o carrinho
    unset($_SESSION['carrinho']);
    if (isset($_COOKIE['revamp_cart'])) {
        setcookie('revamp_cart', '', time() - 3600, "/");
    }

    // 7. Sucesso!
    header("Location: ../pags/sucesso.php?id=" . $pedido_id);
    exit;

} catch (Exception $e) {
    // Se der erro, desfaz tudo
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    die("Erro ao processar o pedido: " . $e->getMessage());
}
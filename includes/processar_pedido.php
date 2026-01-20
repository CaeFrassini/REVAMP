<?php
session_start();
require_once __DIR__ . '/conexao.php';

if (!isset($_SESSION['usuario_id']) || empty($_SESSION['carrinho'])) {
    header("Location: ../index.php");
    exit;
}

$api_token = "abc_dev_QSrwJjzFurpBHydQY3tScamX"; 

$usuario_id = $_SESSION['usuario_id'];
$cep = $_POST['cep'] ?? '';
$rua = $_POST['rua'] ?? '';
$numero = $_POST['numero'] ?? '';
$complemento = $_POST['complemento'] ?? '';
$endereco_completo = $rua . ", " . $numero . " " . $complemento;

try {
    $conn->beginTransaction();

    $total_pedido = 0;
    $itens_para_api = [];
    $itens_detalhados = [];

    foreach ($_SESSION['carrinho'] as $item) {
        $stmt_prod = $conn->prepare("SELECT nome, preco FROM produtos WHERE id = :id");
        $stmt_prod->execute([':id' => $item['id']]);
        $prod = $stmt_prod->fetch(PDO::FETCH_ASSOC);

        if ($prod) {
            $preco_un = $prod['preco'];
            $total_pedido += ($preco_un * $item['quantidade']);

            $itens_detalhados[] = [
                'id' => $item['id'],
                'tam' => $item['tamanho'],
                'qtd' => $item['quantidade'],
                'preco' => $preco_un
            ];

            $itens_para_api[] = [
                "externalId" => (string)$item['id'],
                "name" => $prod['nome'] . " (" . $item['tamanho'] . ")",
                "quantity" => (int)$item['quantidade'],
                "priceUnit" => (int)($preco_un * 100)
            ];
        }
    }

    $sql_ped = "INSERT INTO pedidos (usuario_id, total, endereco, cep, metodo_pagamento, status) 
                VALUES (:uid, :tot, :end, :cep, 'pix', 'pendente')";
    $stmt_ped = $conn->prepare($sql_ped);
    $stmt_ped->execute([
        ':uid' => $usuario_id,
        ':tot' => $total_pedido,
        ':end' => $endereco_completo,
        ':cep' => $cep
    ]);
    $pedido_id = $conn->lastInsertId();

    
    $stmt_item = $conn->prepare("INSERT INTO pedido_itens (pedido_id, produto_id, tamanho, quantidade, preco_unitario) 
                                 VALUES (:pid, :prodid, :tam, :qtd, :prc)");
    
    foreach ($itens_detalhados as $i) {
        $stmt_item->execute([
            ':pid' => $pedido_id, ':prodid' => $i['id'], ':tam' => $i['tam'], ':qtd' => $i['qtd'], ':prc' => $i['preco']
        ]);

       
        $coluna = "estoque_" . strtolower($i['tam']);
        $sql_stk = "UPDATE produtos SET $coluna = $coluna - :q WHERE id = :id";
        $conn->prepare($sql_stk)->execute([':q' => $i['qtd'], ':id' => $i['id']]);
    }

   
    $dados_abacate = [
        "frequency" => "ONE_TIME",
        "methods" => ["PIX"],
        "products" => $itens_para_api,
        "returnUrl" => "Dominio/site" . $pedido_id,
        "completionUrl" => "Dominio/site" . $pedido_id
    ];

    $ch = curl_init("https://api.abacatepay.com/v1/billing/create");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ["Content-Type: application/json", "Authorization: Bearer $api_token"],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($dados_abacate)
    ]);

    $res_api = curl_exec($ch);
    $status_api = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status_api !== 200 && $status_api !== 201) {
        throw new Exception("Falha na API: " . $res_api);
    }

    $json_res = json_decode($res_api, true);
    $url_pagamento = $json_res['data']['url'];

    
    $conn->commit();
    unset($_SESSION['carrinho']);
    setcookie('revamp_cart', '', time() - 3600, "/");

    header("Location: " . $url_pagamento);
    exit;

} catch (Exception $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    die("Erro: " . $e->getMessage());
}
<?php
require_once __DIR__ . '/conexao.php';

$payload = file_get_contents('php://input');
$dados = json_decode($payload, true);


if (!$dados) {
    http_response_code(400);
    exit;
}

if ($dados['event'] === 'billing.paid') {
    
    $metadata = $dados['data'];
    
    $pedido_id = $metadata['externalId'] ?? null;

    if ($pedido_id) {
        try {
            $stmt = $conn->prepare("UPDATE pedidos SET status = 'pago' WHERE id = :id");
            $stmt->execute([':id' => $pedido_id]);
            
            
            http_response_code(200); 
        } catch (Exception $e) {
            http_response_code(500);
        }
    }
} else {
    
    http_response_code(200);
}
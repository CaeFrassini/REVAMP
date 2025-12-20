<?php
require_once 'includes/conexao.php';

try {
    $stmt = $conn->query("SELECT id, nome, imagem FROM produtos ORDER BY id DESC LIMIT 20");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($produtos) === 0) {
        echo "Nenhum produto encontrado\n";
        exit;
    }
    foreach ($produtos as $p) {
        echo $p['id'] . " | " . $p['nome'] . " | " . ($p['imagem'] ?? '(null)') . PHP_EOL;
    }
} catch (PDOException $e) {
    echo "Erro ao consultar produtos: " . $e->getMessage() . PHP_EOL;
}

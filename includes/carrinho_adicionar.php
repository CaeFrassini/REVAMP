<?php
session_start();
require_once __DIR__ . '/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['produto_id'], $_POST['tamanho'])) {
    
    $id = $_POST['produto_id'];
    $tamanho = $_POST['tamanho'];
    
    $item_chave = $id . "_" . $tamanho;

    if (!isset($_SESSION['carrinho']) || !is_array($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = array();
    }

    if (isset($_SESSION['carrinho'][$item_chave])) {
        $_SESSION['carrinho'][$item_chave]['quantidade'] += 1;
    } else {
        
        $_SESSION['carrinho'][$item_chave] = [
            'id' => $id,
            'tamanho' => $tamanho,
            'quantidade' => 1
        ];
    }

    header("Location: ../pags/bag.php");
    exit;

} else {
    header("Location: ../index.php");
    exit;
}
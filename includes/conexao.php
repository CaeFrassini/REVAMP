<?php
$host = 'localhost';
$db_name = 'revamp';
$username = 'root';
$password = ''; 
// DSN com charset
$dsn = "mysql:host={$host};dbname={$db_name};charset=utf8mb4";

// Opções recomendadas para PDO
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_PERSISTENT => false,
];

try {
    $conn = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    // Loga o erro para o servidor, e mostra mensagem genérica ao usuário
    error_log('Database connection error: ' . $e->getMessage());
    echo 'Erro na conexão com o banco de dados. Tente novamente mais tarde.';
    exit;
}
?>
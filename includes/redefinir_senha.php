<?php
require_once __DIR__ . '/conexao.php';

$token = $_GET['token'] ?? '';

if (empty($token)) {
    die('Token não fornecido.');
}

try {
    // Verifique se o nome da tabela no banco é exatamente 'recuperacao_Senha' (com S maiúsculo)
    $sql = "SELECT * FROM recuperacao_Senha WHERE token = :token AND data_expiracao > NOW() AND usado = 0";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':token' => $token]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die('Erro ao verificar token: ' . $e->getMessage());
}

if (!$pedido) {
    die('Este link é inválido, já foi usado ou expirou. Por favor, solicite a recuperação novamente.');
}

$error = ''; // Inicializa a variável de erro

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $senha = $_POST['senha'] ?? '';
    $senha_confirm = $_POST['senha_confirm'] ?? '';

    if (empty($senha) || $senha !== $senha_confirm) {
        $error = 'As senhas não coincidem ou estão vazias.';
    } else {
        $nova_senha = password_hash($senha, PASSWORD_DEFAULT);
        $email = $pedido['email'];

        try {
            $conn->beginTransaction();

            $sql_update = "UPDATE usuarios SET senha = :senha WHERE email = :email";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->execute([':senha' => $nova_senha, ':email' => $email]);

            $sql_token = "UPDATE recuperacao_Senha SET usado = 1 WHERE token = :token";
            $stmt_token = $conn->prepare($sql_token);
            $stmt_token->execute([':token' => $token]);

            $conn->commit();

            header('Location: ../login.php?msg=senha_atualizada');
            exit();
        } catch (Exception $e) {
            $conn->rollBack();
            $error = 'Erro ao redefinir a senha: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - REVAMP</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="top-announcement-bar">
        <div class="ticker-content">
            <span>SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972 •SUPORTE PELO WHATSAPP 11 99898-6972  </span>
            <span>SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972 • SUPORTE PELO WHATSAPP 11 99898-6972 </span>
        </div>
    </div>
    <main class="login-wrapper">
        <div class="login-box">
            <h2>Criar Nova Senha</h2>
            
            <?php if (!empty($error)): ?>
                <p style="color: #ff4d4d; font-size: 12px; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($error); ?>
                </p>
            <?php endif; ?>

            <form method="POST">
                <div class="input-field">
                    <input type="password" name="senha" id="senha" placeholder=" " required>
                    <label for="senha">Nova senha</label>
                </div>
                <div class="input-field">
                    <input type="password" name="senha_confirm" id="senha_confirm" placeholder=" " required>
                    <label for="senha_confirm">Confirme a senha</label>
                </div>
                <button type="submit" class="login-btn">Atualizar Senha</button>
            </form>
            
            <div class="login-options" style="justify-content: center; margin-top: 20px;">
                <a href="../login.php">Voltar ao Login</a>
            </div>
        </div>
    </main>
</body>
</html>
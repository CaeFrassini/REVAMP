<?php
session_start();
require_once __DIR__ . '/../../includes/conexao.php';

// 1. Proteção de Acesso
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../login.php');
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$mensagem = '';
$tipo_mensagem = ''; 

// 2. Carregar dados atuais do usuário
try {
    $stmt = $conn->prepare('SELECT id, nome, email FROM usuarios WHERE id = :id');
    $stmt->execute([':id' => $usuario_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        header('Location: ../../includes/logout.php');
        exit();
    }
} catch (Exception $e) {
    die('Erro ao buscar usuário: ' . $e->getMessage());
}

// 3. Verificar sucesso via GET
if (isset($_GET['sucesso']) && $_GET['sucesso'] == '1') {
    $mensagem = 'Perfil atualizado com sucesso.';
    $tipo_mensagem = 'sucesso';
}

// 4. Processar o Formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    if (!$nome || !$email) {
        $mensagem = 'Nome e e-mail são obrigatórios.';
        $tipo_mensagem = 'erro';
    } else {
        try {
            // Verifica se o e-mail já existe em outro ID
            $sql_check = "SELECT id FROM usuarios WHERE email = :email AND id != :id";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->execute([':email' => $email, ':id' => $usuario_id]);

            if ($stmt_check->rowCount() > 0) {
                $mensagem = 'Este e-mail já está em uso.';
                $tipo_mensagem = 'erro';
            } else {
                if (!empty($senha)) {
                    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                    $sql = 'UPDATE usuarios SET nome = :nome, email = :email, senha = :senha WHERE id = :id';
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([':nome' => $nome, ':email' => $email, ':senha' => $senha_hash, ':id' => $usuario_id]);
                } else {
                    $sql = 'UPDATE usuarios SET nome = :nome, email = :email WHERE id = :id';
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([':nome' => $nome, ':email' => $email, ':id' => $usuario_id]);
                }

                $_SESSION['usuario_nome'] = $nome;
                header('Location: editar_perfil.php?sucesso=1');
                exit();
            }
        } catch (Exception $e) {
            $mensagem = 'Erro: ' . $e->getMessage();
            $tipo_mensagem = 'erro';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - REVAMP</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>
    <header class="header-container">
        <nav>
            <a href="../../index.php">SHOP</a>
            <a href="../gallery.php">PHOTOS</a>
            <a href="../sac.php">SAC</a>
            <div class="nav-right-icons">
                <a href="bag.php"><i class="fas fa-shopping-bag"></i></a>
                
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <a href="pags/profile/minha_conta.php"><i class="fas fa-user"></i></a>
                <?php else: ?>
                    <a href="login.php"><i class="fas fa-user"></i></a>
                <?php endif; ?>
                </div>
        </nav>
    </header>
    <main class="login-wrapper">
        <div class="login-box">
            <h2>EDITAR PERFIL</h2>

            <?php if ($mensagem): ?>
                <p style="color: <?php echo ($tipo_mensagem == 'sucesso') ? '#00ff41' : '#ff4d4d'; ?>; font-family: 'Source Code Pro'; font-size: 12px; text-align: center;">
                    [ <?php echo htmlspecialchars($mensagem); ?> ]
                </p>
            <?php endif; ?>

            <form method="POST">
                <div class="input-field">
                    <input type="text" name="nome" id="nome" pattern="[A-Za-zÀ-ÿ\s]+" title="O nome deve conter apenas letras." value="<?php echo htmlspecialchars($user['nome']); ?>" required>
                    <label for="nome">NOME</label>
                </div>

                <div class="input-field">
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    <label for="email">E-MAIL</label>
                </div>
                <button type="submit" class="login-btn">SALVAR</button>
            </form>

            <div class="login-options" style="justify-content: center; margin-top: 20px;">
                <a href="minha_conta.php">VOLTAR</a>
            </div>
        </div>
    </main>
</body>
</html>
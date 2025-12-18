<?php
require_once 'includes/conexao.php';

// Variáveis para controlar os pop-ups
$status = ""; 
$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['password'];
    $confirmar_senha = $_POST['confirm_password'];

    if($senha !== $confirmar_senha){
        $status = "erro_senha";
        $mensagem = "As senhas não coincidem.";
    } else {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        try {
            // 1. Verificar se e-mail existe
            $checkEmail = $conn->prepare("SELECT id FROM usuarios WHERE email = :email");
            $checkEmail->bindParam(':email', $email);
            $checkEmail->execute();

            if ($checkEmail->rowCount() > 0){
                $status = "erro_email";
                $mensagem = "Este e-mail já está cadastrado.";
            } else {
                // 2. Inserir novo usuário
                $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";
                $stmt = $conn->prepare($sql);
                
                $stmt->bindParam(':nome', $nome);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':senha', $senha_hash);

                if ($stmt->execute()){
                    $status = "sucesso";
                    $mensagem = "Sua conta foi criada com sucesso!";
                }
            }
        } catch (PDOException $e) {
            $status = "erro_sistema";
            $mensagem = "Erro crítico: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REVAMP🌐-CADASTRO</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=BBH+Bartle&family=Saira:ital,wght@0,100..900;1,100..900&family=Source+Code+Pro:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header-container">
        <nav>
            <a href="index.php">SHOP</a>
            <a href="pags/gallery.php">PHOTOS</a>
            <a href="sac.php">SAC</a>
            <div class="nav-right-icons">
                <a href="bag.php"><i class="fas fa-shopping-bag"></i></a>
                <a href="login.php"><i class="fas fa-user icon-link"></i></a>
            </div>
        </nav>
    </header>

    <main class="login-wrapper">
        <div class="login-box">
            <form action="regs.php" method="POST">
                <h2>CADASTRO</h2>
                
                <div class="input-field">
                    <input type="text" name="nome" id="nome" placeholder=" " required>
                    <label for="nome">Nome Completo</label>
                </div>

                <div class="input-field">
                    <input type="email" name="email" id="email" placeholder=" " required>
                    <label for="email">E-mail</label>
                </div>

                <div class="input-field">
                    <input type="password" name="password" id="password" placeholder=" " required>
                    <label for="password">Senha</label>
                </div>

                <div class="input-field">
                    <input type="password" name="confirm_password" id="confirm_password" placeholder=" " required>
                    <label for="confirm_password">Confirmar Senha</label>
                </div>

                <button type="submit" class="login-btn">CRIAR CONTA</button>

                <div class="login-options" style="justify-content: center;">
                    <a href="login.php">Já tem uma conta? Entrar</a>
                </div>
            </form>
        </div>
    </main>

    <script>
        <?php if ($status == "sucesso"): ?>
            Swal.fire({
                title: 'CONTA CRIADA!',
                text: '<?php echo $mensagem; ?>',
                icon: 'success',
                confirmButtonColor: '#000',
                confirmButtonText: 'IR PARA LOGIN'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'login.php';
                }
            });
        <?php elseif ($status != ""): ?>
            Swal.fire({
                title: 'OPS...',
                text: '<?php echo $mensagem; ?>',
                icon: 'error',
                confirmButtonColor: '#000',
                confirmButtonText: 'TENTAR NOVAMENTE'
            });
        <?php endif; ?>
    </script>
</body>
</html>
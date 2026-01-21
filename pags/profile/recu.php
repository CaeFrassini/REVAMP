<?php
session_start();
require_once __DIR__ . '/../../includes/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);

    $redirect = 'recu.php?status=sucesso';

    if ($email) {
        $stmt = $conn->prepare('SELECT id, email FROM usuarios WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $token = bin2hex(random_bytes(16));

            $sql = 'INSERT INTO recuperacao_Senha (email, token, data_expiracao, usado) VALUES (:email, :token, DATE_ADD(NOW(), INTERVAL 1 HOUR), 0)';
            $ins = $conn->prepare($sql);
            $ins->execute([':email' => $email, ':token' => $token]);

            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $resetLink = "http://{$host}/REVAMP/pags/profile/redefinir_senha.php?token={$token}";

            
            $subject = 'Recuperação de senha - REVAMP';
            $message = "Olá,\n\nClique no link abaixo para redefinir sua senha (válido por 1 hora):\n\n" . $resetLink . "\n\nSe você não solicitou, ignore este e-mail.";
            $headers = 'From: no-reply@' . $host . "\r\n";

            @mail($email, $subject, $message, $headers);
        }
    }

    header('Location: ' . $redirect);
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RECUPERAÇÃO 🌐</title>
    <link rel="stylesheet" href="/REVAMP/assets/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Saira:wght@100..900&family=Source+Code+Pro:wght@200..900&display=swap" rel="stylesheet">
</head>
<body>
    <div class="top-announcement-bar">
        <div class="ticker-content">
            <span>MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐  </span>
            <span>MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐 • MAKE YOUR WORLD 🌐 </span>
        </div>
    </div>
    <header class="header-container">
            <nav>
            <a href="/REVAMP/index.php">SHOP</a>
            <a href="/REVAMP/gallery.php">PHOTOS</a>
            <a href="/REVAMP/sac.php">SAC</a>
                <div class="nav-right-icons">
                <a href="/REVAMP/bag.php"><i class="fas fa-shopping-bag"></i></a>
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <a href="/REVAMP/pags/profile/minha_conta.php"><i class="fas fa-user"></i></a>
                <?php else: ?>
                    <a href="/REVAMP/login.php"><i class="fas fa-user icon-link"></i></a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="login-wrapper">
        <div class="login-box">
            <form action="recu.php" method="POST">
                <h2>ALTERAR SENHA</h2>
                <p>Por favor, insira seu e-mail para receber as instruções para mudar a senha.</p>
                
                <div class="input-field">
                    <input type="email" name="email" id="email" placeholder=" " required>
                    <label for="email">E-mail</label>
                </div>

                <button type="submit" class="login-btn">ALTERAR SENHA</button>

                <div class="login-options" style="justify-content: center;">
                    <a href="../../login.php">Voltar ao Login</a>
                </div>
            </form>
        </div>
    </main> <?php if(isset($_GET['status']) && $_GET['status'] == 'sucesso'): ?>
    <script>
        Swal.fire({
            title: 'Enviado!',
            text: 'Se o e-mail existir, você receberá as instruções.',
            icon: 'success',
            confirmButtonColor: '#000'
        });
    </script>
    <?php endif; ?>

</body>
</html>
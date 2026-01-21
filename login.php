<?php
session_start();
require_once __DIR__ . '/includes/conexao.php';

$erro = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = $_POST['email'];
    $senha = $_POST['password'];

    try{
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if($usuario && password_verify($senha, $usuario['senha'])){
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            header("Location: index.php");
            exit();
        } else{
            $erro = "E-mail ou senha inválidos.";
        }
    }catch (PDOException $e){
        $erro = "Erro ao processar o login: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN 🌐</title>
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
                    <a href="/REVAMP/login.php"><i class="fas fa-user"></i></a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="login-wrapper">
        <div class="login-box">
            <form action="login.php" method="POST">
                <h2>LOGIN</h2>
                <div class="input-field">
                    <input type="email" name="email" id="email" placeholder=" " required>
                    <label for="email">E-mail*</label>
                </div>

                <div class="input-field">
                    <input type="password" name="password" id="password" placeholder=" " required>
                    <label for="password">Senha*</label>
                </div>

                <button type="submit" class="login-btn">ENTRAR</button>

                <div class="login-options">
                    <a href="/REVAMP/pags/profile/recu.php">Esqueceu a senha?</a>
                    <a href="/REVAMP/pags/profile/regs.php">Cadastrar-se</a>
                </div>
            </form>
        </div>
    </main>

    <script>
        <?php if ($erro != ""): ?>
            Swal.fire({
                title: 'Erro no Login',
                text: '<?php echo $erro; ?>',
                icon: 'error',
                confirmButtonColor: '#000'
            });
        <?php endif; ?>
    </script> </body>
</html>
<?php
session_start();

require_once __DIR__ . '/../../includes/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

try {
    $query = "SELECT nome, email, data_cadastro FROM usuarios WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->execute(['id' => $usuario_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header("Location: ../../includes/logout.php");
        exit();
    }
} catch (PDOException $e) {
    die("Erro ao carregar dados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MINHA CONTA | REVAMP🌐</title>
    
    <link rel="stylesheet" href="../../assets/css/styles.css"> 
    
    <link href="https://fonts.googleapis.com/css2?family=Saira:wght@100..900&family=Source+Code+Pro:wght@200..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

    <header class="header-container">
            <nav>
            <a href="../../index.php">SHOP</a>
            <a href="../gallery.php">PHOTOS</a>
            <a href="../sac.php">SAC</a>
            <div class="nav-right-icons">
                <a href="../../bag.php"><i class="fas fa-shopping-bag"></i></a>
                <a href="../../includes/logout.php" title="Sair"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </nav>
    </header>

    <main class="account-wrapper">
        <div class="account-container">
            <header class="account-header">
                <h1>MINHA CONTA</h1>
                <p class="welcome-msg">STATUS: AUTENTICADO COMO <?php echo strtoupper(htmlspecialchars($user['nome'])); ?></p>
            </header>

            <section class="profile-grid">
                <div class="info-card">
                    <span class="info-label">IDENTIFICAÇÃO</span>
                    <p class="info-value"><?php echo htmlspecialchars(strtoupper($user['nome'])); ?></p>
                </div>

                <div class="info-card">
                    <span class="info-label">E-MAIL</span>
                    <p class="info-value"><?php echo htmlspecialchars($user['email']); ?></p>
                    <a href="editar_perfil.php" class="btn-action">EDITAR PERFIL</a>
                </div>

                <div class="info-card">
                    <span class="info-label">MEMBRO DESDE</span>
                    <p class="info-value">
                        <?php 
                            // Formata a data de cadastro para o padrão brasileiro
                            echo date('d/m/Y', strtotime($user['data_cadastro'])); 
                        ?>
                    </p>
                </div>

                <div class="info-card">
                    <span class="info-label">SEGURANÇA</span>
                    <p class="info-value">••••••••••••</p>
                </div>
            </section>

            <div class="logout-section">
                <a href="../../includes/logout.php" class="logout-link">[ SAIR ]</a>
            </div>
        </div>
    </main>

    <footer class="footer">
        <p>REVAMP🌐 &copy; <?php echo date('Y'); ?></p>
    </footer>

</body>
</html>
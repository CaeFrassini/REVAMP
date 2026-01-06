<?php
// Inicia a sessão para ter acesso aos dados que serão apagados
session_start();

// Limpa todas as variáveis de sessão (como usuario_id, nome, etc)
$_SESSION = array();

// Se desejar destruir o cookie da sessão também (mais seguro)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destrói a sessão no servidor
session_destroy();

// Redireciona o usuário para a página de login que está na raiz
header("Location: ../login.php");
exit();
?>
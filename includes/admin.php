<?php
require_once 'conexao.php';

$status = "";
$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Pegando dados do formulário (ajustado para bater com os nomes dos inputs)
    $nome      = $_POST['name'];
    $preco     = $_POST['price'];
    $categoria = $_POST['category'];
    $descricao = $_POST['description'];
    $tamanho   = $_POST['size'];
    $estoque   = $_POST['stock'];

    // Lógica de Upload da Imagem
    if (isset($_FILES['product_img']) && $_FILES['product_img']['error'] === 0) {
        $arquivo = $_FILES['product_img'];
        $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        $novoNome = md5(time() . $arquivo['name']) . "." . $extensao;
        $diretorio = "../assets/img/produtos/"; // Certifique-se que esta pasta existe

        if(move_uploaded_file($arquivo['tmp_name'], $diretorio . $novoNome)) {
            try {
                $sql = "INSERT INTO produtos (nome, preco, categoria, descricao, tamanho, estoque, imagem) 
                        VALUES (:nome, :preco, :categoria, :descricao, :tamanho, :estoque, :imagem)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':nome'      => $nome,
                    ':preco'     => $preco,
                    ':categoria' => $categoria,
                    ':descricao' => $descricao,
                    ':tamanho'   => $tamanho,
                    ':estoque'   => $estoque,
                    ':imagem'    => $novoNome
                ]);
                $status = "sucesso";
            } catch (PDOException $e) {
                $status = "erro";
                $mensagem = "Erro no banco: " . $e->getMessage();
            }
        } else {
            $status = "erro";
            $mensagem = "Falha ao mover arquivo para o servidor.";
        }
    } else {
        $status = "erro";
        $mensagem = "Selecione uma imagem válida.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>REVAMP🌐- ADM</title>
    <link rel="stylesheet" href="../assets/css/styles-adm.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <header class="header-container">
        <nav>
            <a href="../index.php">SHOP</a>
            <a href="../pags/gallery.php">PHOTOS</a>
            <a href="../sac.php">SAC</a>
            <div class="nav-right-icons">
                <a href="bag.php"><i class="fas fa-shopping-bag"></i></a>
                <a href="../login.php"><i class="fas fa-user"></i></a>
            </div>
        </nav>
    </header>

    <main class="login-wrapper">
        <div class="login-box">
            <form action="" method="POST" enctype="multipart/form-data">
                <h2>NOVO PRODUTO</h2>
                
                <div class="file-upload">
                    <input type="file" name="product_img" id="product_img" accept="image/*" required>
                    <label for="product_img" style="cursor: pointer; display: block;">
                        <div id="upload-placeholder">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p class="main-text">CLIQUE PARA ENVIAR IMAGEM</p>
                        </div>
                        <img id="image-preview" src="" alt="Sua imagem">
                        <p class="sub-text" id="file-name">Nenhum arquivo selecionado</p>
                    </label>
                </div>

                <div class="input-field">
                    <input type="text" name="name" id="name" placeholder=" " required>
                    <label for="name">Nome do Produto</label>
                </div>

                <div style="display: flex; gap: 20px;">
                    <div class="input-field" style="flex: 1;">
                        <input type="number" name="price" id="price" step="0.01" placeholder=" " required>
                        <label for="price">Preço (R$)</label>
                    </div>
                    <div class="input-field" style="flex: 1;">
                        <input type="number" name="stock" id="stock" placeholder=" " required>
                        <label for="stock">Estoque</label>
                    </div>
                </div>

                <div class="input-field">
                    <select name="size" id="size" required>
                        <option value="" disabled selected></option>
                        <option value="P">P</option>
                        <option value="M">M</option>
                        <option value="G">G</option>
                        <option value="GG">GG</option>
                    </select>
                    <label for="size">Tamanho</label>
                </div>

                <div class="input-field">
                    <select name="category" id="category" required>
                        <option value="" disabled selected></option>
                        <option value="tees">T-SHIRTS</option>
                        <option value="hoodies">HOODIES</option>
                    </select>
                    <label for="category">Categoria</label>
                </div>

                <div class="input-field">
                    <textarea name="description" id="description" rows="2" placeholder=" " required></textarea>
                    <label for="description">Descrição</label>
                </div>

                <button type="submit" class="login-btn">ADICIONAR AO SHOP</button>
            </form>
        </div>
    </main>

    <script src="../assets/js/script.js"></script>
    
    <script>
        // Este bloco lida com os avisos do PHP
        <?php if ($status == "sucesso"): ?>
            Swal.fire({ title: 'SUCESSO!', text: 'Produto cadastrado.', icon: 'success', confirmButtonColor: '#000' });
        <?php elseif ($status == "erro"): ?>
            Swal.fire({ title: 'ERRO!', text: '<?php echo $mensagem; ?>', icon: 'error', confirmButtonColor: '#000' });
        <?php endif; ?>
    </script>
</body>
</html>
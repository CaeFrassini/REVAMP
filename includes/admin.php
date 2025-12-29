<?php
require_once __DIR__ . '/conexao.php';

$status = "";
$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome      = $_POST['name'];
    $preco     = $_POST['price'];
    $categoria = $_POST['category'];
    $descricao = $_POST['description'];
    
    $estoque_p  = $_POST['stock_p'] ?? 0;
    $estoque_m  = $_POST['stock_m'] ?? 0;
    $estoque_g  = $_POST['stock_g'] ?? 0;
    $estoque_gg = $_POST['stock_gg'] ?? 0;

    if (isset($_FILES['product_img']) && $_FILES['product_img']['error'] === 0) {
        $arquivo = $_FILES['product_img'];
        $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        $novoNome = md5(time() . $arquivo['name']) . "." . $extensao;
        $diretorio = __DIR__ . "/../assets/img/produtos/";

        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0755, true);
        }

        if(move_uploaded_file($arquivo['tmp_name'], $diretorio . $novoNome)) {
            try {
                $sql = "INSERT INTO produtos (nome, preco, categoria, descricao, estoque_p, estoque_m, estoque_g, estoque_gg, imagem) 
                        VALUES (:nome, :preco, :categoria, :descricao, :p, :m, :g, :gg, :imagem)";
                
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':nome'      => $nome,
                    ':preco'     => $preco,
                    ':categoria' => $categoria,
                    ':descricao' => $descricao,
                    ':p'         => $estoque_p,
                    ':m'         => $estoque_m,
                    ':g'         => $estoque_g,
                    ':gg'        => $estoque_gg,
                    ':imagem'    => $novoNome
                ]);
                $status = "sucesso";
            } catch (PDOException $e) {
                $status = "erro";
                $mensagem = "Erro no banco: " . $e->getMessage();
            }
        } else {
            $status = "erro";
            $mensagem = "Falha ao mover arquivo.";
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
            <a href="../pags/sac.php">SAC</a>
            <div class="nav-right-icons">
                <a href="../bag.php"><i class="fas fa-shopping-bag"></i></a>
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

                <div class="input-field">
                    <input type="number" name="price" id="price" step="0.01" placeholder=" " required>
                    <label for="price">Preço (R$)</label>
                </div>

                <div class="grade-estoque-container">
                    <h3 class="grade-estoque-titulo">Estoque por Grade</h3>
                    
                    <div class="input-field">
                        <input type="number" name="stock_p" id="stock_p" placeholder="0" required>
                        <label for="stock_p">Qtd P</label>
                    </div>

                    <div class="input-field">
                        <input type="number" name="stock_m" id="stock_m" placeholder="0" required>
                        <label for="stock_m">Qtd M</label>
                    </div>

                    <div class="input-field">
                        <input type="number" name="stock_g" id="stock_g" placeholder="0" required>
                        <label for="stock_g">Qtd G</label>
                    </div>

                    <div class="input-field">
                        <input type="number" name="stock_gg" id="stock_gg" placeholder="0" required>
                        <label for="stock_gg">Qtd GG</label>
                    </div>
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
        <?php if ($status == "sucesso"): ?>
            Swal.fire({ title: 'SUCESSO!', text: 'Produto cadastrado.', icon: 'success', confirmButtonColor: '#000' });
        <?php elseif ($status == "erro"): ?>
            Swal.fire({ title: 'ERRO!', text: '<?php echo $mensagem; ?>', icon: 'error', confirmButtonColor: '#000' });
        <?php endif; ?>
    </script>
</body>
</html>
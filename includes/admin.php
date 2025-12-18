<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REVAMP🌐-ADM</title>
    <link rel="stylesheet" href="../assets/css/styles-adm.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Saira:wght@100..900&family=Source+Code+Pro:wght@200..900&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header-container">
        <nav>
            <a href="../index.php">SHOP</a>
            <a href="../pags/gallery.php">PHOTOS</a>
            <a href="../sac.php">SAC</a>
            <div class="nav-right-icons">
                <a href="bag.php"><i class="fas fa-shopping-bag"></i></a>
                <a href="login.php"><i class="fas fa-user icon-link"></i></a>
            </div>
        </nav>
    </header>

    <main class="login-wrapper">
        <div class="login-box product-box">
            <form action="upload.php" method="POST" enctype="multipart/form-data">
                <h2>NOVO PRODUTO</h2>
                
                <div class="file-upload">
                    <input type="file" name="product_img" id="product_img" accept="image/*" required>
                    <label for="product_img">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>UPLOAD IMAGE</span>
                    </label>
                </div>

                <div class="input-field">
                    <input type="text" name="name" id="name" placeholder=" " required>
                    <label for="name">Nome do Produto</label>
                </div>

                <div class="input-field">
                    <input type="text" name="price" id="price" placeholder=" " required>
                    <label for="price">Preço (R$)</label>
                </div>

                <div class="input-field">
                    <select name="category" id="category" required>
                        <option value="" disabled selected></option>
                        <option value="tees">T-SHIRTS</option>
                        <option value="hoodies">HOODIES</option>
                        <option value="accessories">ACCESSORIES</option>
                    </select>
                    <label for="category">Categoria</label>
                </div>

                <div class="input-field">
                    <textarea name="description" id="description" rows="3" placeholder=" " required></textarea>
                    <label for="description">Descrição</label>
                </div>

                <button type="submit" class="login-btn">ADICIONAR AO SHOP</button>
            </form>
        </div>
    </main>
</body>
</html>
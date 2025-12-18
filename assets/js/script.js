// Preview de Imagem
const productImgInput = document.getElementById('product_img');

if (productImgInput) {
    productImgInput.addEventListener('change', function() {
        const file = this.files[0];
        const preview = document.getElementById('image-preview');
        const placeholder = document.getElementById('upload-placeholder');
        const nameText = document.getElementById('file-name');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
                nameText.textContent = file.name;
                nameText.style.color = "#fff";
            }
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
            placeholder.style.display = 'block';
            nameText.textContent = "Nenhum arquivo selecionado";
        }
    });
}

// Botão WhatsApp (se existir na página)
const whatsappBtn = document.getElementById("whatsapp-float");
if (whatsappBtn) {
    whatsappBtn.addEventListener("click", function () {
        window.open("https://chat.whatsapp.com/LyCM1uKQoG5FZozoF4Vhbd", "_blank");
    });
}
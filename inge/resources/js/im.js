document.addEventListener('DOMContentLoaded', () => {
    const inputImagen = document.getElementById('imagen');
    const previewImg = document.getElementById('preview');
    // ==== Vista previa de imagen ====
    inputImagen.addEventListener('change', () => {
        const file = inputImagen.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            previewImg.src = '';
            previewImg.style.display = 'none';
        }
    });
});
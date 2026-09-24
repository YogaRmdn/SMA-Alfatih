<script>
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                preview.innerHTML = `<img src="${e.target.result}" class="h-full w-full object-cover" alt="Preview">`;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function resizeAndPreviewImage(input, previewId, width, height) {
        const file = input.files && input.files[0];
        const preview = document.getElementById(previewId);
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            preview.innerHTML = `<img src="${e.target.result}" class="h-full w-full object-cover" alt="Preview">`;
        };
        reader.readAsDataURL(file);

        if (!file.type.startsWith('image/')) return;

        const url = URL.createObjectURL(file);
        const img = new Image();
        img.onload = () => {
            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            const ratio = width / height;
            const imgRatio = img.width / img.height;
            let sx = 0, sy = 0, sw = img.width, sh = img.height;
            if (imgRatio > ratio) {
                sw = Math.round(img.height * ratio);
                sx = Math.round((img.width - sw) / 2);
            } else {
                sh = Math.round(img.width / ratio);
                sy = Math.round((img.height - sh) / 2);
            }
            ctx.imageSmoothingEnabled = true;
            ctx.imageSmoothingQuality = 'high';
            ctx.drawImage(img, sx, sy, sw, sh, 0, 0, width, height);
            URL.revokeObjectURL(url);
            canvas.toBlob((blob) => {
                if (!blob) return;
                const resized = new File([blob], file.name.replace(/\.[^.]+$/, '') + '.jpg', { type: 'image/jpeg' });
                const dt = new DataTransfer();
                dt.items.add(resized);
                input.files = dt.files;
            }, 'image/jpeg', 0.9);
        };
        img.onerror = () => URL.revokeObjectURL(url);
        img.src = url;
    }
</script>

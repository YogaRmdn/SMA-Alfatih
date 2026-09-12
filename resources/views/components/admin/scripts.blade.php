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
</script>

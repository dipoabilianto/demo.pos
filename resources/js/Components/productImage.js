export function initProductImageUpload() {
    document.addEventListener('change', function(e) {
        if (e.target.id !== 'product-image-input') return;
        var file = e.target.files[0];
        if (!file) return;
        var preview = document.getElementById('product-image-preview');
        var text = document.getElementById('product-image-text');
        var status = document.getElementById('product-image-status');
        var hidden = document.getElementById('product-image-data');
        status.className = 'mt-2 text-xs font-medium text-theme-primary';
        status.textContent = 'Mengompres...';
        status.classList.remove('hidden');
        var canvas = document.createElement('canvas');
        var ctx = canvas.getContext('2d');
        var img = new Image();
        img.onload = function() {
            var w = img.width, h = img.height;
            if (w > 600 || h > 600) {
                var ratio = Math.min(600 / w, 600 / h);
                w = Math.round(w * ratio);
                h = Math.round(h * ratio);
            }
            canvas.width = w;
            canvas.height = h;
            ctx.drawImage(img, 0, 0, w, h);
            canvas.toBlob(function(blob) {
                var reader = new FileReader();
                reader.onloadend = function() {
                    hidden.value = reader.result;
                    preview.innerHTML = '<img src="' + reader.result + '" class="h-32 w-auto object-contain rounded-xl border border-warm-200">';
                    preview.classList.remove('hidden');
                    text.textContent = file.name + ' (' + (blob.size / 1024).toFixed(1) + ' KB)';
                    status.className = 'mt-2 text-xs font-medium text-emerald-600';
                    status.textContent = 'Siap diunggah';
                };
                reader.readAsDataURL(blob);
            }, 'image/webp', 0.8);
        };
        img.onerror = function() {
            status.className = 'mt-2 text-xs font-medium text-rose-600';
            status.textContent = 'Gagal membaca gambar.';
        };
        img.src = URL.createObjectURL(file);
    });
}

export function initStockToggle() {
    document.addEventListener('DOMContentLoaded', function() {
        var cb = document.querySelector('[name="is_unlimited"]');
        if (cb && cb.checked) {
            document.getElementById('stock-input').disabled = true;
            document.getElementById('min-stock-input').disabled = true;
            document.getElementById('stock-label').classList.add('text-warm-400');
            document.getElementById('min-stock-label').classList.add('text-warm-400');
        }
    });
    document.addEventListener('change', function(e) {
        if (e.target.name !== 'is_unlimited') return;
        var checked = e.target.checked;
        document.getElementById('stock-input').disabled = checked;
        document.getElementById('min-stock-input').disabled = checked;
        document.getElementById('stock-label').classList.toggle('text-warm-400', checked);
        document.getElementById('min-stock-label').classList.toggle('text-warm-400', checked);
    });
}

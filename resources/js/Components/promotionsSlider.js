export default (config) => ({
    items: config.items,
    uploading: false,
    uploadPromoImage(event, item, i) {
        var file = event.target.files[0];
        if (!file) return;
        this.uploading = true;
        var form = new FormData();
        form.append('image', file);
        form.append('promo_id', item.id);
        form.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        fetch(config.uploadUrl, {
            method: 'POST', body: form
        }).then(function(r) { return r.json(); }).then(function(d) {
            if (d.success) {
                item.image = d.path;
            }
            this.uploading = false;
        }.bind(this)).catch(function() {
            this.uploading = false;
        }.bind(this));
    },
});

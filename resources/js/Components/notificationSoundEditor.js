export default (config) => ({
    preset: config.preset,
    soundFile: config.soundFile,
    soundFileName: config.soundFileName,
    soundFileUrl: config.soundFileUrl,
    uploading: false,
    uploadError: '',
    uploadSound(event) {
        var file = event.target.files[0];
        if (!file) return;
        this.uploading = true;
        this.uploadError = '';
        var form = new FormData();
        form.append('file', file);
        form.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        fetch(config.uploadUrl, {
            method: 'POST', body: form
        }).then(function(r) { return r.json(); }).then(function(d) {
            if (d.success) {
                this.soundFile = true;
                this.soundFileName = d.filename;
                this.soundFileUrl = d.path;
                this.preset = 'custom';
            } else {
                this.uploadError = 'Gagal mengunggah file.';
            }
            this.uploading = false;
        }.bind(this)).catch(function() {
            this.uploading = false;
            this.uploadError = 'Terjadi kesalahan.';
        }.bind(this));
    },
    removeSound() {
        this.soundFile = false;
        this.soundFileName = '';
        this.soundFileUrl = '';
        if (this.preset === 'custom') this.preset = 'nada1';
    },
});

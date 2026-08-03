export default () => ({
    show: false,
    loading: false,
    error: '',
    locationText: 'Mendeteksi lokasi...',
    shifts: [],
    location: { lat: null, lng: null },
    form: { shift_id: '', opening_balance: 0 },
    init() {
        var self = this;
        fetch('/attendances/current')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.attendance) return;
                self.shifts = data.shifts || [];
                if (data.shift) self.form.shift_id = data.shift.id;
                if (!self.shifts.length) self.shifts = data.shift ? [data.shift] : [];
                self.show = true;
            })
            .catch(function() {});
        if ('geolocation' in navigator) {
            navigator.geolocation.getCurrentPosition(function(pos) {
                self.location = { lat: pos.coords.latitude, lng: pos.coords.longitude };
                self.locationText = 'Lokasi terdeteksi';
            }, function() {
                self.locationText = 'Lokasi tidak tersedia (izinkan akses lokasi)';
            }, { timeout: 10000 });
        } else {
            self.locationText = 'Browser tidak mendukung geolokasi';
        }
    },
    checkIn() {
        var self = this;
        self.error = '';
        self.loading = true;
        var body = new FormData();
        body.append('shift_id', self.form.shift_id);
        body.append('opening_balance', self.form.opening_balance || 0);
        if (self.location.lat) body.append('lat', self.location.lat);
        if (self.location.lng) body.append('lng', self.location.lng);
        fetch('/attendances/check-in', {
            method: 'POST', body: body,
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
        })
            .then(function(r) { return r.json().then(function(d) { return { status: r.status, data: d }; }); })
            .then(function(res) {
                self.loading = false;
                if (res.status === 422) {
                    self.error = res.data.error || 'Gagal absen';
                    return;
                }
                if (res.data.warning) Alpine.store('toastManager').warning(res.data.warning);
                Alpine.store('toastManager').success(res.data.message || 'Absen hadir berhasil');
                self.show = false;
            })
            .catch(function() {
                self.loading = false;
                self.error = 'Terjadi kesalahan. Coba lagi.';
            });
    },
});

export default () => ({
    status: null,
    loading: false,
    error: '',
    locationText: 'Mendeteksi lokasi...',
    checkInTime: '-',
    checkOutTime: '-',
    openingBalance: 0,
    closingBalance: 0,
    diff: 0,
    todayShift: null,
    location: { lat: null, lng: null },
    form: { closing_balance: '' },
    init() {
        var self = this;
        fetch('/attendances/current')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data.attendance) {
                    self.status = null;
                    return;
                }
                var a = data.attendance;
                self.todayShift = a.shift ? a.shift.name + ' (' + (a.shift.start_time || '') + '\u2013' + (a.shift.end_time || '') + ' WIB)' : '-';
                self.checkInTime = a.check_in_time ? a.check_in_time.substring(0, 5) : '-';
                self.openingBalance = a.opening_balance || 0;
                if (a.check_out_time) {
                    self.checkOutTime = a.check_out_time.substring(0, 5);
                    self.closingBalance = a.closing_balance || 0;
                    self.diff = (a.closing_balance || 0) - (a.opening_balance || 0);
                    self.status = 'checkout';
                } else {
                    self.status = 'checkin';
                }
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
    checkOut() {
        var self = this;
        if (!self.form.closing_balance || self.form.closing_balance < 0) {
            self.error = 'Masukkan jumlah uang akhir';
            return;
        }
        self.error = '';
        self.loading = true;
        var body = new FormData();
        body.append('closing_balance', self.form.closing_balance);
        if (self.location.lat) body.append('lat', self.location.lat);
        if (self.location.lng) body.append('lng', self.location.lng);
        fetch('/attendances/check-out', {
            method: 'POST', body: body,
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
        })
            .then(function(r) { return r.json().then(function(d) { return { status: r.status, data: d }; }); })
            .then(function(res) {
                self.loading = false;
                if (res.status === 422) {
                    self.error = res.data.error || 'Gagal absen pulang';
                    return;
                }
                if (res.data.warning) Alpine.store('toastManager').warning(res.data.warning);
                Alpine.store('toastManager').success(res.data.message || 'Absen pulang berhasil');
                self.status = 'checkout';
                self.checkOutTime = res.data.attendance?.check_out_time?.substring(0, 5) || '-';
                self.closingBalance = res.data.attendance?.closing_balance || 0;
                self.diff = res.data.diff || 0;
            })
            .catch(function() {
                self.loading = false;
                self.error = 'Terjadi kesalahan. Coba lagi.';
            });
    },
});

## Objective
- Menambahkan notifikasi order masuk real-time di dashboard, opsi "Bayar Di Kasir" di halaman publik, serta menyelesaikan berbagai perbaikan UI/UX di settings, footer, modal payment, dan unlimited produk.

## Important Details
- Background publik (animated-bg + blobs) bisa diatur via Settings → Tampilan (3 color picker: Dasar, Gradien, Blob). CSS vars `--bg-base`, `--bg-gradient`, `--bg-blob` dipakai di `app.css` via `color-mix`.
- Dashboard (app layout) ikut berubah warna latar via `var(--bg-base)` — solid, tanpa animasi/blob. Header/footer pakai `color-mix(in srgb, var(--bg-base), transparent/white)`.
- 18 preset warna (Classic s/d Cherry, Monochrome, Hitam Putih, Dark Mode) — masing-masing dengan bg_base, bg_gradient, bg_blob yang cocok. `applyColors()` sekarang set bg vars juga.
- Polling notifikasi dashboard: `/api/orders/latest` hanya return order dgn `payment_status IN (paid, success)`. Poling tiap 15 detik, beep via Web Audio API, popup glass pojok kanan bawah (auto-hide 20 detik), tombol "Proses" → `/orders/{id}`, Browser Notification API saat tab tidak aktif.
- "Bayar Di Kasir" di publik: info banner amber "datang ke kasir" muncul saat cash dipilih. Notifikasi dashboard hanya muncul setelah payment sukses (cash = langsung, online = via webhook).

## Work State
### Completed
- **Settings → Tampilan Web**: input `catalog_title` dipakai di title kedua catalog view. Section dipisah rapi (Info Toko vs Tampilan Web).
- **Background publik atur**: 3 color picker (`bg_base`, `bg_gradient`, `bg_blob`) di tab Tampilan. CSS vars di `public.blade.php`. `animated-bg` + blobs pakai `var(--bg-base/gradient/blob)` via `color-mix`.
- **Dashboard ikut tema**: `app.blade.php` — tambah CSS vars, html `bg-warm-50` → `var(--bg-base)`, header/footer pakai `color-mix`. `applyColors()` set bg vars.
- **Preset diperbanyak**: 15 → 18 preset (termasuk Monochrome, Hitam Putih, Dark Mode) — semua isi bg colors. Layout grid 5 kolom.
- **Footer public-catalog**: glass bg, `animate-fadeUp`, ukuran teks `text-sm`/`text-xs`, ikon `h-4 w-4`.
- **Modal Xendit disederhanakan**: `payment.blade.php` + `public-payment.blade.php` — `rounded-xl shadow-lg`, tanpa ring/icon header, loading spinner saja, `anim-scaleIn` tetap.
- **Bug fix unlimited cart**: `public-catalog.blade.php:590` — `submitCheckout()` filter `p.is_unlimited || p.stock === undefined || p.stock >= qty`.
- **Notifikasi order masuk**:
  - Route `GET /api/orders/latest` (auth) — return order `payment_status IN (paid, success)`, include `id, order_number, customer_name, total, item_count, created_at`.
  - JS polling di `app.blade.php` — Web Audio beep 3 nada, popup glass-strong pojok kanan bawah dengan tombol Proses/Nanti, Browser Notification API saat tab hidden, auto-hide 20 detik.
- **Bayar Di Kasir + filter notifikasi**: `public-payment.blade.php` — tambah button cash + info banner amber. Route filter paid/success. Controller `publicPayment()` sekarang pass `$cashMethod`.

### Active
- *(none)*

### Blocked
- *(none)*

## Next Move
- *(none)* — menunggu arahan user selanjutnya.

## Relevant Files
- `routes/web.php:60-71`: route `/api/orders/latest` (auth), query filter `paid/success`
- `app/Http/Controllers/SettingsController.php`: validasi + allowedFields untuk `bg_base`, `bg_gradient`, `bg_blob`
- `app/Http/Controllers/OrderController.php:311`: `publicPayment()` now passes `$cashMethod` to view
- `resources/views/layouts/app.blade.php`: CSS vars bg, header/footer `color-mix`, polling notifikasi JS + popup HTML
- `resources/views/layouts/public.blade.php`: CSS vars bg (bg_base, bg_gradient, bg_blob)
- `resources/views/settings/general.blade.php`: color picker bg di tab Tampilan, input catalog_title di Tampilan Web
- `resources/views/orders/public-catalog.blade.php`: fix unlimited cart `submitCheckout()`
- `resources/views/orders/public-payment.blade.php`: tambah button "Bayar Di Kasir" + info banner amber; JS toggle label/cashInfo
- `resources/views/orders/payment.blade.php`: modal Xendit disederhanakan
- `resources/css/app.css`: `.animated-bg` pakai `var(--bg-base/gradient)`, `.blob` pakai `var(--bg-blob)` via `color-mix`
- `resources/js/app.js`: `themeEditor` — 18 preset include bg colors, `applyColors()` set bg vars

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Daftar semua permission
    |--------------------------------------------------------------------------
    | Setiap module memiliki key dan label untuk ditampilkan di UI.
    */

    'modules' => [
        [
            'key' => 'dashboard',
            'label' => 'Dashboard',
            'permissions' => [
                ['key' => 'dashboard.view', 'label' => 'Lihat Dashboard'],
            ],
        ],
        [
            'key' => 'products',
            'label' => 'Produk',
            'permissions' => [
                ['key' => 'products.view', 'label' => 'Lihat Produk'],
                ['key' => 'products.create', 'label' => 'Tambah Produk'],
                ['key' => 'products.edit', 'label' => 'Edit Produk'],
                ['key' => 'products.delete', 'label' => 'Hapus Produk'],
            ],
        ],
        [
            'key' => 'categories',
            'label' => 'Kategori',
            'permissions' => [
                ['key' => 'categories.view', 'label' => 'Lihat Kategori'],
                ['key' => 'categories.create', 'label' => 'Tambah Kategori'],
                ['key' => 'categories.edit', 'label' => 'Edit Kategori'],
                ['key' => 'categories.delete', 'label' => 'Hapus Kategori'],
            ],
        ],
        [
            'key' => 'orders',
            'label' => 'Pesanan',
            'permissions' => [
                ['key' => 'orders.view', 'label' => 'Lihat Pesanan'],
                ['key' => 'orders.create', 'label' => 'Buat Pesanan'],
            ],
        ],
        [
            'key' => 'sales',
            'label' => 'Penjualan',
            'permissions' => [
                ['key' => 'sales.view', 'label' => 'Lihat Penjualan'],
                ['key' => 'sales.create', 'label' => 'Buat Penjualan'],
            ],
        ],
        [
            'key' => 'payments',
            'label' => 'Pembayaran',
            'permissions' => [
                ['key' => 'payments.process', 'label' => 'Proses Pembayaran'],
            ],
        ],
        [
            'key' => 'receipts',
            'label' => 'Resi',
            'permissions' => [
                ['key' => 'receipts.view', 'label' => 'Lihat Resi'],
            ],
        ],
        [
            'key' => 'expenses',
            'label' => 'Pengeluaran',
            'permissions' => [
                ['key' => 'expenses.view', 'label' => 'Lihat Pengeluaran'],
                ['key' => 'expenses.create', 'label' => 'Tambah Pengeluaran'],
                ['key' => 'expenses.edit', 'label' => 'Edit Pengeluaran'],
                ['key' => 'expenses.delete', 'label' => 'Hapus Pengeluaran'],
            ],
        ],
        [
            'key' => 'raw-materials',
            'label' => 'Bahan Baku',
            'permissions' => [
                ['key' => 'raw-materials.view', 'label' => 'Lihat Bahan Baku'],
                ['key' => 'raw-materials.create', 'label' => 'Tambah Bahan Baku'],
                ['key' => 'raw-materials.edit', 'label' => 'Edit Bahan Baku'],
                ['key' => 'raw-materials.delete', 'label' => 'Hapus Bahan Baku'],
            ],
        ],
        [
            'key' => 'stock-opname',
            'label' => 'Stok Opname',
            'permissions' => [
                ['key' => 'stock-opname.view', 'label' => 'Lihat Stok Opname'],
                ['key' => 'stock-opname.adjust', 'label' => 'Adjust Stok'],
                ['key' => 'stock-opname.history', 'label' => 'Riwayat Stok'],
            ],
        ],
        [
            'key' => 'vouchers',
            'label' => 'Voucher',
            'permissions' => [
                ['key' => 'vouchers.view', 'label' => 'Lihat Voucher'],
                ['key' => 'vouchers.create', 'label' => 'Tambah Voucher'],
                ['key' => 'vouchers.edit', 'label' => 'Edit Voucher'],
                ['key' => 'vouchers.delete', 'label' => 'Hapus Voucher'],
            ],
        ],
        [
            'key' => 'users',
            'label' => 'Pengguna',
            'permissions' => [
                ['key' => 'users.view', 'label' => 'Lihat Pengguna'],
                ['key' => 'users.create', 'label' => 'Tambah Pengguna'],
                ['key' => 'users.edit', 'label' => 'Edit Pengguna'],
                ['key' => 'users.delete', 'label' => 'Hapus Pengguna'],
            ],
        ],
        [
            'key' => 'roles',
            'label' => 'Role & Hak Akses',
            'permissions' => [
                ['key' => 'roles.view', 'label' => 'Lihat Role'],
                ['key' => 'roles.create', 'label' => 'Tambah Role'],
                ['key' => 'roles.edit', 'label' => 'Edit Role'],
                ['key' => 'roles.delete', 'label' => 'Hapus Role'],
            ],
        ],
        [
            'key' => 'settings',
            'label' => 'Pengaturan',
            'permissions' => [
                ['key' => 'settings.view', 'label' => 'Lihat Pengaturan'],
                ['key' => 'settings.update', 'label' => 'Ubah Pengaturan'],
                ['key' => 'settings.general', 'label' => 'Atur Informasi Toko'],
                ['key' => 'settings.notifications', 'label' => 'Atur Notifikasi'],
                ['key' => 'settings.receipt', 'label' => 'Atur Struk & Printer'],
                ['key' => 'settings.promotions', 'label' => 'Atur Promosi'],
                ['key' => 'settings.tax', 'label' => 'Atur Pajak'],
                ['key' => 'settings.appearance', 'label' => 'Atur Tampilan'],
                ['key' => 'settings.payment', 'label' => 'Atur Pembayaran (Xendit)'],
            ],
        ],
        [
            'key' => 'payment-methods',
            'label' => 'Metode Pembayaran',
            'permissions' => [
                ['key' => 'payment-methods.view', 'label' => 'Lihat Metode Bayar'],
                ['key' => 'payment-methods.toggle', 'label' => 'Aktif/Nonaktif Metode'],
            ],
        ],
        [
            'key' => 'reports',
            'label' => 'Laporan',
            'permissions' => [
                ['key' => 'reports.view', 'label' => 'Lihat Laporan'],
                ['key' => 'reports.sales', 'label' => 'Laporan Penjualan'],
                ['key' => 'reports.expenses', 'label' => 'Laporan Pengeluaran'],
                ['key' => 'reports.stock', 'label' => 'Laporan Stok'],
                ['key' => 'reports.raw-materials', 'label' => 'Laporan Bahan Baku'],
                ['key' => 'reports.stock-opname', 'label' => 'Laporan Stok Opname'],
                ['key' => 'reports.financial', 'label' => 'Laporan Keuangan'],
            ],
        ],
        [
            'key' => 'security',
            'label' => 'Keamanan',
            'permissions' => [
                ['key' => 'security.manage', 'label' => 'Kelola Keamanan (2FA)'],
            ],
        ],
        [
            'key' => 'attendances',
            'label' => 'Absensi',
            'permissions' => [
                ['key' => 'attendances.check-in', 'label' => 'Absen Masuk/Pulang'],
                ['key' => 'attendances.report', 'label' => 'Lihat Laporan Absensi'],
            ],
        ],
        [
            'key' => 'shifts',
            'label' => 'Shift',
            'permissions' => [
                ['key' => 'shifts.view', 'label' => 'Lihat Shift'],
                ['key' => 'shifts.create', 'label' => 'Tambah Shift'],
                ['key' => 'shifts.edit', 'label' => 'Edit/Hapus Shift'],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default permissions per role
    |--------------------------------------------------------------------------
    | Gunakan wildcard * untuk semua permission dalam satu module.
    | Contoh: 'products.*' berarti semua permission di module products.
    */

    'role_defaults' => [
        'admin' => [
            'dashboard.*',
            'products.*',
            'categories.*',
            'orders.*',
            'sales.*',
            'payments.*',
            'receipts.*',
            'expenses.*',
            'raw-materials.*',
            'stock-opname.*',
            'vouchers.*',
            'users.*',
            'roles.*',
            'settings.*',
            'payment-methods.*',
            'reports.*',
            'attendances.*',
            'shifts.*',
        ],
        'kasir' => [
            'dashboard.*',
            'orders.*',
            'sales.*',
            'payments.*',
            'receipts.*',
            'attendances.check-in',
        ],
        'produksi' => [
            'dashboard.*',
            'products.*',
            'categories.*',
            'attendances.check-in',
        ],
        'gudang' => [
            'dashboard.*',
            'products.view',
            'categories.view',
            'expenses.*',
            'raw-materials.*',
            'stock-opname.*',
            'reports.view',
            'reports.raw-materials',
            'reports.stock',
            'reports.stock-opname',
            'attendances.check-in',
        ],
        'owner' => [
            'dashboard.*',
            'orders.*',
            'sales.*',
            'receipts.*',
            'reports.*',
            'settings.*',
            'payment-methods.*',
            'attendances.*',
            'shifts.*',
        ],
        'superadmin' => [], // superadmin bypasses all checks
    ],

];

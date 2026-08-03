<?php

namespace Database\Seeders;

use App\Models\RawMaterial;
use Illuminate\Database\Seeder;

class RawMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            ['name' => 'Tepung Terigu Protein Tinggi', 'unit' => 'Kg', 'current_stock' => 50, 'min_stock' => 10],
            ['name' => 'Tepung Terigu Protein Sedang', 'unit' => 'Kg', 'current_stock' => 30, 'min_stock' => 10],
            ['name' => 'Gula Pasir', 'unit' => 'Kg', 'current_stock' => 25, 'min_stock' => 5],
            ['name' => 'Gula Halus', 'unit' => 'Kg', 'current_stock' => 10, 'min_stock' => 3],
            ['name' => 'Mentega', 'unit' => 'Kg', 'current_stock' => 15, 'min_stock' => 5],
            ['name' => 'Margarin', 'unit' => 'Kg', 'current_stock' => 12, 'min_stock' => 5],
            ['name' => 'Telur', 'unit' => 'Butir', 'current_stock' => 200, 'min_stock' => 50],
            ['name' => 'Susu Bubuk', 'unit' => 'Kg', 'current_stock' => 8, 'min_stock' => 3],
            ['name' => 'Ragi Instan', 'unit' => 'Pack', 'current_stock' => 20, 'min_stock' => 5],
            ['name' => 'Coklat Batang', 'unit' => 'Kg', 'current_stock' => 5, 'min_stock' => 2],
            ['name' => 'Keju Mozarella', 'unit' => 'Kg', 'current_stock' => 3, 'min_stock' => 2],
            ['name' => 'Keju Cheddar', 'unit' => 'Kg', 'current_stock' => 4, 'min_stock' => 2],
            ['name' => 'Sosis Sapi', 'unit' => 'Pack', 'current_stock' => 15, 'min_stock' => 5],
            ['name' => 'Abon Sapi', 'unit' => 'Kg', 'current_stock' => 2, 'min_stock' => 1],
            ['name' => 'Kornet Sapi', 'unit' => 'Kaleng', 'current_stock' => 24, 'min_stock' => 6],
            ['name' => 'Selai Nanas', 'unit' => 'Kg', 'current_stock' => 5, 'min_stock' => 2],
            ['name' => 'Pasta Pandan', 'unit' => 'ml', 'current_stock' => 100, 'min_stock' => 30],
            ['name' => 'Vanili Bubuk', 'unit' => 'Pack', 'current_stock' => 10, 'min_stock' => 3],
            ['name' => 'Baking Powder', 'unit' => 'Pack', 'current_stock' => 8, 'min_stock' => 3],
            ['name' => 'Baking Soda', 'unit' => 'Pack', 'current_stock' => 6, 'min_stock' => 2],
            ['name' => 'Garam', 'unit' => 'Kg', 'current_stock' => 5, 'min_stock' => 2],
            ['name' => 'Minyak Goreng', 'unit' => 'Liter', 'current_stock' => 10, 'min_stock' => 3],
            ['name' => 'Susu Cair UHT', 'unit' => 'Liter', 'current_stock' => 3, 'min_stock' => 5],
            ['name' => 'Kismis', 'unit' => 'Kg', 'current_stock' => 2, 'min_stock' => 1],
            ['name' => 'Kenari Cincang', 'unit' => 'Kg', 'current_stock' => 1, 'min_stock' => 1],
        ];

        foreach ($materials as $material) {
            RawMaterial::firstOrCreate(
                ['name' => $material['name']],
                $material
            );
        }

        $this->command->info('25 bahan baku berhasil ditambahkan.');
    }
}

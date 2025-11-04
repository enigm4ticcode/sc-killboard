<?php

namespace Database\Seeders;

use App\Models\Manufacturer;
use Illuminate\Database\Seeder;

class ManufacturerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manufacturers = [
            // Known manufacturers - Original
            ['code' => 'HRST', 'name' => 'Hurston Dynamics'],
            ['code' => 'MXOX', 'name' => 'MaxOx'],
            ['code' => 'KLWE', 'name' => 'Klaus & Werner'],
            ['code' => 'AMRS', 'name' => 'Amon & Reese'],
            ['code' => 'BEHR', 'name' => 'Behring'],

            // Vehicle Weapon Manufacturers
            ['code' => 'AEGS', 'name' => 'Aegis Dynamics'],
            ['code' => 'ANVL', 'name' => 'Anvil Aerospace'],
            ['code' => 'APAR', 'name' => 'Apocalypse Arms'],
            ['code' => 'ARGO', 'name' => 'Argo Astronautics'],
            ['code' => 'CRUS', 'name' => 'Crusader Industries'],
            ['code' => 'ESPR', 'name' => 'Esperia'],
            ['code' => 'GATS', 'name' => 'Gallenson Tactical Systems'],
            ['code' => 'KRON', 'name' => 'Kroneg'],
            ['code' => 'MISC', 'name' => 'MISC'],
            ['code' => 'ORIG', 'name' => 'Origin Jumpworks'],
            ['code' => 'VNCL', 'name' => 'Vanduul'],

            // FPS Weapon Manufacturers (lowercase codes)
            ['code' => 'gmni', 'name' => 'Gemini'],
            ['code' => 'hdgw', 'name' => 'Hedeby Gunworks'],
            ['code' => 'ksar', 'name' => 'Kastak Arms'],
            ['code' => 'lbco', 'name' => 'Lightning Bolt Co.'],
            ['code' => 'volt', 'name' => 'VOLT'],

            // Default for weapons without manufacturer
            ['code' => 'NONE', 'name' => 'Unknown'],
        ];

        foreach ($manufacturers as $manufacturer) {
            Manufacturer::updateOrCreate(
                ['code' => $manufacturer['code']],
                ['name' => $manufacturer['name']]
            );
        }
    }
}

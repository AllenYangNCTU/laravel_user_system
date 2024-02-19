<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AddressType;

class AddressTypeSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        AddressType::create([
            'name' => 'Residential Address',
            'is_active' => true,
        ]);

        AddressType::create([
            'name' => 'Correspondence Address',
            'is_active' => true,
        ]);
    }
}
<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@cika.test'],
            ['name' => 'Admin CIKA', 'password' => Hash::make('admin12345')]
        );

        $categories = [
            'NOC' => 'Panduan operasional Network Operation Center',
            'Teknisi' => 'Panduan pekerjaan teknisi lapangan',
            'Customer Service' => 'Panduan layanan pelanggan',
            'Administrasi' => 'Panduan administrasi internal',
            'Network' => 'Panduan jaringan dan perangkat',
        ];

        foreach ($categories as $name => $description) {
            Category::firstOrCreate(
                ['name' => $name],
                ['description' => $description, 'status' => 'active']
            );
        }
    }
}

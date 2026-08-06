<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete old admin account if exists
        User::where('email', 'admin@aspacindo.com')->delete();

        // Super Admin — username: aspacindo, password: aspac123
        User::updateOrCreate(
            ['email' => 'aspacindo'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('aspac123'),
                'role'     => 'super_admin',
            ]
        );

        // Viewer — read-only, no password required
        User::updateOrCreate(
            ['email' => 'viewer@aspacindo.com'],
            [
                'name'     => 'Viewer',
                'password' => Hash::make(''),
                'role'     => 'viewer',
            ]
        );
    }
}

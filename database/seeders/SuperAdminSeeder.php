<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $user = User::updateOrCreate(
            ['email' => 'yoseph.iriandi.tambunan@gmail.com'],
            [
                'name' => 'Yoseph Iriandi',
                'password' => Hash::make('#T4mbun4n#'),
                'email_verified_at' => now(),
            ]
        );

        if (!$user->hasRole('super_admin')) {
            $user->assignRole($role);
        }
    }
}

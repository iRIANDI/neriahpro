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

        // 1. Primary Superadmin
        $admin = User::updateOrCreate(
            ['email' => 'yoseph.iriandi.tambunan@gmail.com'],
            [
                'name' => 'Yoseph Iriandi Tambunan',
                'password' => Hash::make('#T4mbun4n#'),
                'email_verified_at' => now(),
            ]
        );

        if (!$admin->hasRole('super_admin')) {
            $admin->assignRole($role);
        }

        // 2. Dedicated Midtrans QA / Reviewer Dummy Account
        $reviewer = User::updateOrCreate(
            ['email' => 'reviewer.midtrans@neriahpro.com'],
            [
                'name' => 'Midtrans QA Reviewer',
                'password' => Hash::make('MidtransDemo2026#'),
                'email_verified_at' => now(),
            ]
        );

        if (!$reviewer->hasRole('super_admin')) {
            $reviewer->assignRole($role);
        }
    }
}

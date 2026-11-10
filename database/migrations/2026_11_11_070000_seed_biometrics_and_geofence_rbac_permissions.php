<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Permission;
use App\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $perms = [
            'manage_biometrics_and_liveness',
            'purge_biometrics_right_to_erasure',
            'manage_geofence_and_spatial_zones',
            'approve_out_of_office_presence',
        ];

        foreach ($perms as $permName) {
            $perm = Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
            
            // Give to super_admin
            $superAdmin = Role::where('name', 'super_admin')->where('guard_name', 'web')->first();
            if ($superAdmin && ! $superAdmin->hasPermissionTo($perm)) {
                $superAdmin->givePermissionTo($perm);
            }

            // Strictly revoke from midtrans_reviewer
            $reviewer = Role::where('name', 'midtrans_reviewer')->where('guard_name', 'web')->first();
            if ($reviewer && $reviewer->hasPermissionTo($perm)) {
                $reviewer->revokePermissionTo($perm);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe no-op to preserve audit history
    }
};

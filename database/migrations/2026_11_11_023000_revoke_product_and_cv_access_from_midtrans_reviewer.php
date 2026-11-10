<?php

declare(strict_types=1);

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('roles') && Schema::hasTable('permissions')) {
            $reviewerRole = Role::where('name', 'midtrans_reviewer')->where('guard_name', 'web')->first();

            if ($reviewerRole) {
                $forbidden = [
                    'ViewAny:Product',
                    'View:Product',
                    'Create:Product',
                    'Update:Product',
                    'Delete:Product',
                    'view_cv_pro_finance',
                ];

                foreach ($forbidden as $permName) {
                    $p = Permission::where('name', $permName)->where('guard_name', 'web')->first();
                    if ($p && $reviewerRole->hasPermissionTo($p)) {
                        $reviewerRole->revokePermissionTo($p);
                    }
                }
            }

            // Flush permission cache
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op to preserve strict RBAC boundary
    }
};

<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Permission extends SpatiePermission
{
    use HasUlids;
}

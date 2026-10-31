<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Support\PosRealWorldRuntimeCatalog;
use Illuminate\Database\Seeder;

class PosArchitecturePillarSeeder extends Seeder
{
    public function run(): void
    {
        PosRealWorldRuntimeCatalog::seedFifteenArchitecturePillars();
    }
}


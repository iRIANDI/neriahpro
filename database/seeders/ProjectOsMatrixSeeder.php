<?php

namespace Database\Seeders;

use App\Support\ProjectOsMatrixCatalog;
use Illuminate\Database\Seeder;

class ProjectOsMatrixSeeder extends Seeder
{
    public function run(): void
    {
        ProjectOsMatrixCatalog::seedDatabase();
    }
}


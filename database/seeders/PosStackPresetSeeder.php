<?php

namespace Database\Seeders;

use App\Support\PosStackPresetCatalog;
use Illuminate\Database\Seeder;

class PosStackPresetSeeder extends Seeder
{
    /**
     * Seed the 9 Curated Tech Stack & Topology Presets (Monolith, Decoupled Headless, Microservices)
     * and enrich all 12 Core Primitives with the 4 Ultimate Zero-Assumption JSONB columns.
     */
    public function run(): void
    {
        PosStackPresetCatalog::seedDatabase();
        \App\Support\PosSectorUiArchetypeCatalog::enrichDatabaseSectors();
        \App\Support\PosDayTwoProductionCatalog::enrichDatabaseSectors();
    }
}


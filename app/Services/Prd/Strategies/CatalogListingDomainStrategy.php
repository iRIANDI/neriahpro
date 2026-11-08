<?php

namespace App\Services\Prd\Strategies;

class CatalogListingDomainStrategy extends AbstractDomainStrategy
{
    public function supports(string $title, string $desc, string $lower): bool
    {
        return str_contains($lower, 'katalog') ||
            str_contains($lower, 'filter') ||
            str_contains($lower, 'cari') ||
            str_contains($lower, 'search') ||
            str_contains($lower, 'produk') ||
            str_contains($lower, 'properti') ||
            str_contains($lower, 'daftar');
    }

    public function analyze(
        string $title,
        string $desc,
        string $businessName,
        string $clientActor,
        string $primaryActor,
        int $index,
        string $prefix,
        array $erdTables = [],
        bool $isEn = false
    ): array {
        $relatedTable = $this->matchTable($title, $desc, $erdTables);

        $ac = [
            [
                'scenario' => 'Filter Reaktif Sub-100ms Tanpa Reload Halaman Penuh',
                'given' => 'Katalog memiliki data yang siap ditampilkan.',
                'when' => 'Pengguna memilih parameter filter (kategori, kisaran harga, atau lokasi).',
                'then' => 'Daftar item diperbarui secara reaktif dalam waktu <100ms dengan mempertahankan posisi scroll dan riwayat URL query string.',
            ],
            [
                'scenario' => 'Ketersediaan Skeleton Loader dan Empty State',
                'given' => 'Pencarian dilakukan dengan kata kunci spesifik.',
                'when' => 'Data sedang diunduh dari server ATAU tidak ada hasil yang cocok.',
                'then' => 'Sistem menampilkan Skeleton Loader shimmer (anti layout-shift) saat loading, atau Empty State empatik dengan tombol Reset Filter jika data nihil.',
            ],
            [
                'scenario' => 'Stabilitas Paginasi Keyset Cursor O(1)',
                'given' => 'Katalog menampung puluhan ribu data.',
                'when' => 'Pengguna melakukan navigasi halaman selanjutnya (Load More).',
                'then' => 'Sistem menggunakan cursorPaginate() berbasis pointer ID ULID sehingga performa query database tetap O(1) konstan tanpa beban memory leak.',
            ],
        ];

        $integration = [
            'endpoint' => 'GET /api/v1/catalog?cursor=...&category=...&q=...',
            'middleware' => ['web', 'throttle:60,1'],
            'request_schema' => '{"q": "Jakarta", "category_id": "01J...", "min_price": 50000000, "cursor": "eyJpZCI..."}',
            'response_schema' => '{"data": [{"id": "01J...", "title": "Unit A", "price": 150000000}], "next_cursor": "eyJpZCI...", "has_more": true}',
            'tasks' => [
                'Daftarkan endpoint API / Livewire Action dengan response JSON terformat.',
                'Integrasikan ETag dan HTTP Cache-Control (max-age=60) untuk optimasi bandwidth.',
            ],
        ];

        $mission = "Bangun modul Katalog & Filter Data Interaktif dengan Keyset Cursor Pagination O(1) dan UI Anti-AI-Slop.";
        $targetFiles = ['app/Models/CatalogItem.php', 'database/migrations/xxxx_create_catalog_items_table.php', 'app/Http/Controllers/CatalogController.php', 'resources/views/catalog/index.blade.php'];
        $verifyCommand = "php artisan test --filter=CatalogFilterTest";

        return [
            'category' => 'DISCOVERY_CATALOG',
            'category_label' => 'Katalog & Pencarian O(1)',
            'related_table' => $relatedTable['name'] ?? 'catalog_items',
            'related_table_data' => $relatedTable,
            'user_story' => "Sebagai {$clientActor}, saya ingin menelusuri katalog data {$businessName} dengan filter dinamis dan pencarian instan, sehingga saya dapat menemukan informasi yang paling cocok dengan kebutuhan saya dalam waktu singkat.",
            'acceptance_criteria' => $ac,
            'frontend' => [
                'design_tokens' => 'Grid asimetris modern (1 kolom mobile, 2 kolom tablet, 3-4 kolom desktop), aspect-ratio 16:9/4:3 gambar dengan object-cover (anti squish), badge kategori HSL berkarakter.',
                'states' => 'Skeleton cards beranimasi shimmer saat fetch data, hover card lift effect (hover:-translate-y-1 hover:shadow-lg transition-all duration-300), empty state dengan tombol Reset.',
                'components' => ['CatalogGrid.blade.php', 'CatalogCard.blade.php', 'CatalogFilterBar.blade.php', 'SkeletonCard.blade.php'],
                'tasks' => [
                    'Bangun komponen grid katalog yang mobile-first dan responsif.',
                    'Implementasikan filter drawer interaktif dengan binding URL query string.',
                    'Sediakan skeleton loader shimmer untuk mencegah cumulative layout shift (CLS = 0).',
                ],
            ],
            'backend' => [
                'model_and_migration' => "Tabel `catalog_items` dengan `->ulid('id')->primary()`, indexes compound `[status, created_at]`, foreignUlid, soft-deletes.",
                'scalability_guardrail' => "Hindari standard paginate() (OFFSET). Wajib gunakan cursorPaginate() dengan ->orderBy('id', 'desc') untuk stabilitas kursor O(1).",
                'tasks' => [
                    'Buat migration tabel katalog dengan ULID primary key dan indeks pada kolom-kolom filter.',
                    'Implementasikan Query Scope `scopeFilter()` di Model untuk menangani parameter pencarian secara efisien.',
                    'Pastikan eager loading `with(...)` diterapkan untuk mematikan ancaman N+1 query problem.',
                ],
            ],
            'integration' => $integration,
            'code_agent_directive' => $this->buildDirective(
                sprintf('FEAT-%s-%02d-CATALOG_FILTER', $prefix, $index),
                $title,
                $businessName,
                $mission,
                $targetFiles,
                $verifyCommand,
                $relatedTable,
                $ac,
                $integration,
                $index
            ),
        ];
    }
}


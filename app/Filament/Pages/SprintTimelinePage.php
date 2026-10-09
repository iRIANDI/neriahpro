<?php

namespace App\Filament\Pages;

use App\Models\CmsGlobalSetting;
use App\Models\LeadContact;
use App\Models\VisionBlueprint;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;

class SprintTimelinePage extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';
    protected static string | \UnitEnum | null $navigationGroup = 'Project Management';
    protected static ?string $navigationLabel = 'Sprint Capacity & Gantt Timeline';
    protected static ?string $title = 'Sprint Capacity Matrix & Anti-Collision Gantt Timeline';
    protected static ?int $navigationSort = 0;

    protected string $view = 'filament.pages.sprint-timeline';

    public ?string $activeTab = 'matrix'; // 'matrix', 'gantt', 'calendar', 'settings'
    public ?string $selectedProjectForAssign = null;
    public ?string $targetBatchForAssign = null;

    /**
     * Access control: Admins, Developers, and Project Managers.
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        if ($user->hasAnyRole(['midtrans_reviewer', 'client_retail', 'client_partner', 'client'])) {
            return false;
        }

        return true;
    }

    public function getHeading(): string | Htmlable
    {
        return 'Sprint Capacity Matrix & Anti-Collision Gantt Timeline';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Pusat Kendali Alokasi Sprint, Manajemen Kapasitas Terkelola (Managed Capacity), Audit Tabrakan Jadwal (Anti-Collision), dan Visualisasi Gantt Multi-Proyek.';
    }

    /**
     * Default Batch Blueprint Configuration.
     */
    public static function getDefaultBatchConfig(): array
    {
        return [
            'batch_1' => [
                'id' => 'batch_1',
                'label' => 'Batch 1',
                'name' => 'Batch 1 (15 Okt - 25 Nov 2026)',
                'dates' => '15 Okt - 25 Nov 2026',
                'start_date' => '2026-10-15',
                'end_date' => '2026-11-25',
                'total_slots' => 3,
                'status' => 'active',
                'urgency_badge' => 'SISA 1 SLOT',
                'description' => 'Siklus sprint utama kuartal 4. Pengerjaan intensif arsitektur, database ULID, dan MVP.',
            ],
            'batch_2' => [
                'id' => 'batch_2',
                'label' => 'Batch 2',
                'name' => 'Batch 2 (01 Des 2026 - 15 Jan 2027)',
                'dates' => '01 Des 2026 - 15 Jan 2027',
                'start_date' => '2026-12-01',
                'end_date' => '2027-01-15',
                'total_slots' => 3,
                'status' => 'upcoming',
                'urgency_badge' => 'TERSEDIA 2 SLOT',
                'description' => 'Siklus sprint transisi akhir tahun & awal tahun baru. Alokasi slot reguler.',
            ],
            'batch_q1_2027' => [
                'id' => 'batch_q1_2027',
                'label' => 'Batch Q1 2027',
                'name' => 'Batch Q1 2027 (Mulai Feb 2027)',
                'dates' => 'Mulai Feb 2027',
                'start_date' => '2027-02-01',
                'end_date' => '2027-03-31',
                'total_slots' => 4,
                'status' => 'reservation',
                'urgency_badge' => 'RESERVASI AWAL',
                'description' => 'Slot booking awal tahun kuartal 1 2027. Early bird priority & scope freezing lock.',
            ],
        ];
    }

    /**
     * Retrieve the configured batches from CmsGlobalSetting or fallback.
     */
    public function getBatchesConfig(): array
    {
        $saved = CmsGlobalSetting::getVal('sprint_batches_config');
        if (is_array($saved) && ! empty($saved)) {
            return $saved;
        }

        return self::getDefaultBatchConfig();
    }

    /**
     * Assign a Vision Blueprint to a Sprint Batch.
     */
    public function assignProject(string $blueprintId, string $batchKey): void
    {
        $blueprint = VisionBlueprint::find($blueprintId);
        if (! $blueprint) {
            Notification::make()->danger()->title('Proyek tidak ditemukan!')->send();
            return;
        }

        $meta = $blueprint->user_metadata ?? [];
        $meta['sprint_batch'] = $batchKey;
        $meta['sprint_batch_assigned_at'] = now()->toIso8601String();
        $blueprint->user_metadata = $meta;
        $blueprint->save();

        Notification::make()
            ->success()
            ->title('Proyek Berhasil Dialokasikan!')
            ->body("Proyek '{$blueprint->nama_bisnis}' berhasil dikunci ke dalam slot {$batchKey}.")
            ->send();
    }

    /**
     * Unassign a Vision Blueprint from its Sprint Batch.
     */
    public function unassignProject(string $blueprintId): void
    {
        $blueprint = VisionBlueprint::find($blueprintId);
        if (! $blueprint) {
            return;
        }

        $meta = $blueprint->user_metadata ?? [];
        unset($meta['sprint_batch'], $meta['sprint_batch_assigned_at']);
        $blueprint->user_metadata = $meta;
        $blueprint->save();

        Notification::make()
            ->info()
            ->title('Alokasi Proyek Dihapus')
            ->body("Proyek '{$blueprint->nama_bisnis}' telah dilepaskan dari slot batch.")
            ->send();
    }

    /**
     * Update Batch Configuration via Settings Tab.
     */
    public function saveBatchesSettings(array $updatedBatches): void
    {
        $setting = CmsGlobalSetting::firstOrNew(['key' => 'sprint_batches_config']);
        $setting->value = $updatedBatches;
        $setting->save();

        Notification::make()
            ->success()
            ->title('Konfigurasi Batch Berhasil Disimpan!')
            ->body('Kapasitas slot dan tanggal sprint berhasil diperbarui secara dinamis.')
            ->send();
    }

    /**
     * Generate Unified Multi-Project Mermaid Gantt Syntax.
     */
    public function generateUnifiedGantt(array $batchesWithProjects): string
    {
        $today = now()->format('Y-m-d');
        $code = "gantt\n";
        $code .= "    title Master Delivery Roadmap: Multi-Project Sprint & Managed Capacity\n";
        $code .= "    dateFormat YYYY-MM-DD\n";
        $code .= "    axisFormat %d %b\n\n";

        $hasTasks = false;

        foreach ($batchesWithProjects as $bKey => $batch) {
            $cleanLabel = preg_replace('/[#:"\r\n]+/', '', $batch['label']);
            $cleanDates = preg_replace('/[#:"\r\n]+/', '', $batch['dates']);
            $code .= "    section {$cleanLabel} ({$cleanDates})\n";

            $assignedProjects = $batch['projects'] ?? [];
            if (empty($assignedProjects)) {
                $code .= "    Slot Kapasitas Terbuka (Siap Booking) :milestone, open_{$bKey}, {$batch['start_date']}, 0d\n";
                $hasTasks = true;
                continue;
            }

            foreach ($assignedProjects as $idx => $proj) {
                $rawName = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', substr($proj['name'], 0, 20));
                $pName = trim($rawName) ?: 'Project ' . ($idx + 1);
                $sDate = $proj['start_date'] ?: $batch['start_date'];
                $prefix = "p_{$bKey}_{$idx}";

                $statusTag = match ($proj['status']) {
                    'Active Sprint', 'In Development (DP Paid)', 'In Progress' => 'active, ',
                    'Completed' => 'done, ',
                    default => '',
                };

                $code .= "    {$pName} - Discovery & DB ULID :{$statusTag}{$prefix}_1, {$sDate}, 7d\n";
                $code .= "    {$pName} - Core Business & Filament :{$prefix}_2, after {$prefix}_1, 14d\n";
                $code .= "    {$pName} - Island UI & QA :{$prefix}_3, after {$prefix}_2, 14d\n";
                $code .= "    {$pName} - Production Go-Live :milestone, {$prefix}_live, after {$prefix}_3, 0d\n";
                $hasTasks = true;
            }
            $code .= "\n";
        }

        if (! $hasTasks) {
            $code .= "    section Inisialisasi Roadmap\n";
            $code .= "    Penyusunan Jadwal Sprint :done, init_1, {$today}, 3d\n";
        }

        return trim($code);
    }

    public function getViewData(): array
    {
        $batchesConfig = $this->getBatchesConfig();

        // 1. Fetch all blueprints
        $blueprints = VisionBlueprint::query()
            ->latest('created_at')
            ->limit(100)
            ->get();

        // 2. Fetch leads that booked sprint batches
        $leads = LeadContact::query()
            ->whereNotNull('metadata->sprint_batch')
            ->latest('created_at')
            ->limit(50)
            ->get();

        // 3. Map blueprints to batches
        $batchesWithProjects = [];
        $totalAssignedSlots = 0;
        $totalMaxSlots = 0;
        $activeSprintCount = 0;

        foreach ($batchesConfig as $key => $batch) {
            $totalMaxSlots += (int) ($batch['total_slots'] ?? 3);

            // Match blueprints
            $matchedBlueprints = $blueprints->filter(function ($b) use ($key, $batch) {
                $bBatch = $b->user_metadata['sprint_batch'] ?? null;
                if ($bBatch === $key || $bBatch === $batch['name'] || $bBatch === $batch['label']) {
                    return true;
                }
                // Check if project status is Active Sprint and created near batch start date
                if (empty($bBatch) && in_array($b->project_status, ['Active Sprint', 'In Development (DP Paid)', 'In Progress']) && $key === 'batch_1') {
                    return true;
                }
                return false;
            });

            // Match leads
            $matchedLeads = $leads->filter(function ($l) use ($key, $batch) {
                $lBatch = $l->metadata['sprint_batch'] ?? '';
                return str_contains($lBatch, $batch['label']) || $lBatch === $key;
            });

            $projectList = [];
            foreach ($matchedBlueprints as $b) {
                $projectList[] = [
                    'id' => $b->id,
                    'type' => 'blueprint',
                    'name' => $b->nama_bisnis ?: ($b->client_name . ' Project'),
                    'client' => $b->client_name,
                    'email' => $b->email,
                    'phone' => $b->phone,
                    'status' => $b->project_status ?: 'Prospecting',
                    'target_waktu' => $b->target_waktu ?: '30 Hari Kerja',
                    'slug' => $b->slug,
                    'public_url' => url('/blueprint/' . $b->slug),
                    'edit_url' => url('/admin/vision-blueprints/' . $b->id . '/edit'),
                    'created_at' => $b->created_at?->format('d M Y'),
                    'start_date' => $b->signed_at?->format('Y-m-d') ?: $batch['start_date'],
                ];
                if (in_array($b->project_status, ['Active Sprint', 'In Development (DP Paid)', 'In Progress'])) {
                    $activeSprintCount++;
                }
            }

            foreach ($matchedLeads as $l) {
                // Ensure not duplicate
                $already = collect($projectList)->firstWhere('email', $l->email);
                if (! $already) {
                    $projectList[] = [
                        'id' => $l->id,
                        'type' => 'lead',
                        'name' => ($l->company_name ?: $l->name) . ' (Konsultasi Lead)',
                        'client' => $l->name,
                        'email' => $l->email,
                        'phone' => $l->phone,
                        'status' => 'Lead Consultation',
                        'target_waktu' => 'Tahap Negosiasi PRD',
                        'slug' => null,
                        'public_url' => null,
                        'edit_url' => url('/admin/lead-contacts/' . $l->id . '/edit'),
                        'created_at' => $l->created_at?->format('d M Y'),
                        'start_date' => $batch['start_date'],
                    ];
                }
            }

            $usedSlots = count($projectList);
            $totalAssignedSlots += $usedSlots;
            $remainingSlots = max(0, (int) $batch['total_slots'] - $usedSlots);

            $batchesWithProjects[$key] = array_merge($batch, [
                'projects' => $projectList,
                'used_slots' => $usedSlots,
                'remaining_slots' => $remainingSlots,
                'utilization_percent' => round(($usedSlots / max(1, (int) $batch['total_slots'])) * 100),
                'is_full' => $remainingSlots === 0,
            ]);
        }

        // Unassigned blueprints
        $unassignedBlueprints = $blueprints->filter(function ($b) use ($batchesWithProjects) {
            $bBatch = $b->user_metadata['sprint_batch'] ?? null;
            if ($bBatch) {
                return false;
            }
            // Check if already matched
            foreach ($batchesWithProjects as $bw) {
                if (collect($bw['projects'])->contains('id', $b->id)) {
                    return false;
                }
            }
            return true;
        });

        // Unified Gantt syntax
        $unifiedGanttMermaid = $this->generateUnifiedGantt($batchesWithProjects);

        return [
            'batches' => $batchesWithProjects,
            'totalMaxSlots' => $totalMaxSlots,
            'totalAssignedSlots' => $totalAssignedSlots,
            'remainingOverallSlots' => max(0, $totalMaxSlots - $totalAssignedSlots),
            'activeSprintCount' => $activeSprintCount,
            'unassignedBlueprints' => $unassignedBlueprints,
            'unifiedGanttMermaid' => $unifiedGanttMermaid,
        ];
    }
}

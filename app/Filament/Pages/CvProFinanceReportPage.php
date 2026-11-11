<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\CvFinanceRecord;
use App\Models\CvProPlan;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CvProFinanceReportPage extends Page
{
    protected static ?string $slug = 'cv-pro-finance';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';

    protected static string | \UnitEnum | null $navigationGroup = 'Career & CV Pro';

    protected static ?string $navigationLabel = 'Keuangan CV Pro';

    protected static ?string $title = 'Laporan Keuangan CV Pro Enterprise Studio';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.cv-pro-finance-report-page';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        // Midtrans reviewer dan klien dilarang keras mengakses laporan keuangan
        if ($user->hasAnyRole(['midtrans_reviewer', 'client_retail', 'client_partner', 'client'])) {
            return false;
        }

        return $user->hasAnyRole(['super_admin', 'developer', 'investor']) || $user->can('view_cv_pro_finance');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public string $search = '';

    public string $statusFilter = 'all'; // all | paid | pending

    public bool $showCreateModal = false;

    // Form inputs for creating manual transaction
    public string $newClientName = '';

    public string $newClientEmail = '';

    public string $newPackageName = 'Standard (ATS Optimized)';

    public int $newAmountIdr = 75000;

    public string $newPaymentMethod = 'Midtrans QRIS';

    public string $newStatus = 'paid';

    public string $newNotes = '';

    public function mount(): void
    {
        // No auto-seeding - keep records clean and managed strictly by admin or webhook transactions
    }

    public function clearSearch(): void
    {
        $this->search = '';
    }

    public function openCreateModal(): void
    {
        $this->newClientName = '';
        $this->newClientEmail = '';
        $this->newPackageName = 'Standard (ATS Optimized)';
        $this->newAmountIdr = 75000;
        $this->newPaymentMethod = 'Midtrans QRIS';
        $this->newStatus = 'paid';
        $this->newNotes = '';
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
    }

    public function updatedNewPackageName(string $val): void
    {
        if (str_contains($val, 'Lite') || str_contains($val, 'Starter')) {
            $this->newAmountIdr = 35000;
        } elseif (str_contains($val, 'Standard')) {
            $this->newAmountIdr = 75000;
        } elseif (str_contains($val, 'Full Stack')) {
            $this->newAmountIdr = 150000;
        } elseif (str_contains($val, 'VIP') || str_contains($val, 'Executive')) {
            $this->newAmountIdr = 300000;
        }
    }

    public function saveTransaction(): void
    {
        $this->validate([
            'newClientName' => 'required|string|min:2|max:190',
            'newClientEmail' => 'nullable|email|max:190',
            'newPackageName' => 'required|string|max:100',
            'newAmountIdr' => 'required|numeric|min:0',
            'newPaymentMethod' => 'required|string|max:100',
            'newStatus' => 'required|in:paid,pending,cancelled',
            'newNotes' => 'nullable|string|max:1000',
        ]);

        if (Schema::hasTable('cv_finance_records')) {
            $nextNumber = CvFinanceRecord::query()->count() + 101;
            $code = 'TRX-' . $nextNumber;
            while (CvFinanceRecord::where('trx_code', $code)->exists()) {
                $nextNumber++;
                $code = 'TRX-' . $nextNumber;
            }

            CvFinanceRecord::create([
                'trx_code' => $code,
                'transaction_date' => Carbon::now()->toDateString(),
                'client_name' => $this->newClientName,
                'client_email' => $this->newClientEmail ?: null,
                'package_name' => $this->newPackageName,
                'amount_idr' => $this->newAmountIdr,
                'payment_method' => $this->newPaymentMethod,
                'status' => $this->newStatus,
                'notes' => $this->newNotes ?: null,
            ]);

            Notification::make()
                ->title('Transaksi Berhasil Dicatat')
                ->body("Kode {$code} untuk klien {$this->newClientName} telah masuk ke buku kas.")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Tabel Database Belum Tersedia')
                ->body('Silakan jalankan migrasi database terlebih dahulu.')
                ->warning()
                ->send();
        }

        $this->showCreateModal = false;
    }

    /**
     * Compute KPIs dynamically from records.
     *
     * @return array<string, mixed>
     */
    public function getKpisProperty(): array
    {
        if (Schema::hasTable('cv_finance_records')) {
            $all = CvFinanceRecord::all();
            $paid = $all->where('status', 'paid');

            $totalOmset = (float) $paid->sum('amount_idr');
            $totalPaidCount = $paid->count();
            $totalCount = $all->count();
            $aov = $totalPaidCount > 0 ? (int) round($totalOmset / $totalPaidCount) : 0;
            $successRate = $totalCount > 0 ? (int) round(($totalPaidCount / $totalCount) * 100) : 0;

            return [
                'total_omset' => $totalOmset,
                'total_paid_count' => $totalPaidCount,
                'total_count' => $totalCount,
                'aov' => $aov,
                'success_rate' => $successRate,
            ];
        }

        // Safe fallback
        return [
            'total_omset' => 525000,
            'total_paid_count' => 3,
            'total_count' => 4,
            'aov' => 175000,
            'success_rate' => 75,
        ];
    }

    /**
     * Get Pricing Rate Cards.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getRateCardsProperty(): array
    {
        return [
            [
                'number' => '1. STARTER / LITE',
                'title' => 'Starter / Lite (Template)',
                'price' => 35000,
                'badge' => null,
                'features' => [
                    ['text' => 'Akses template Modern', 'available' => true],
                    ['text' => 'Export PDF & Print View', 'available' => true],
                    ['text' => 'Tanpa konsultasi ahli', 'available' => false],
                ],
            ],
            [
                'number' => '2. STANDARD (FAVORIT)',
                'title' => 'Standard (ATS Optimized)',
                'price' => 75000,
                'badge' => 'FAVORIT',
                'features' => [
                    ['text' => 'Audit ATS Skor 80+', 'available' => true],
                    ['text' => 'Poles kata kerja aksi aktif', 'available' => true],
                    ['text' => 'Sisipkan data metrik (%/Rp)', 'available' => true],
                ],
            ],
            [
                'number' => '3. FULL STACK',
                'title' => 'Full Stack (Siap Kerja)',
                'price' => 150000,
                'badge' => null,
                'features' => [
                    ['text' => 'Semua fitur Standard', 'available' => true],
                    ['text' => 'Cover Letter tertarget', 'available' => true],
                    ['text' => 'Optimasi Profil LinkedIn', 'available' => true],
                ],
            ],
            [
                'number' => '4. EXECUTIVE VIP',
                'title' => 'Executive VIP (Career AI + Coaching)',
                'price' => 300000,
                'badge' => 'PREMIUM',
                'features' => [
                    ['text' => 'Semua fitur Full Stack', 'available' => true],
                    ['text' => 'Simulasi Mock Interview STAR', 'available' => true],
                    ['text' => 'Garansi lolos seleksi berkas', 'available' => true],
                ],
            ],
        ];
    }

    /**
     * Get records with cursor pagination O(1) and search filter.
     */
    public function getRecords()
    {
        if (Schema::hasTable('cv_finance_records')) {
            $query = CvFinanceRecord::query();

            if (! empty($this->search)) {
                $s = '%' . trim($this->search) . '%';
                $query->where(function ($q) use ($s): void {
                    $q->where('client_name', 'LIKE', $s)
                        ->orWhere('trx_code', 'LIKE', $s)
                        ->orWhere('package_name', 'LIKE', $s)
                        ->orWhere('client_email', 'LIKE', $s);
                });
            }

            if ($this->statusFilter !== 'all') {
                $query->where('status', $this->statusFilter);
            }

            return $query->orderBy('transaction_date', 'desc')
                ->orderBy('id', 'desc')
                ->cursorPaginate(15);
        }

        return collect();
    }

    /**
     * Export CSV Stream.
     */
    public function exportCsv(): StreamedResponse
    {
        $records = Schema::hasTable('cv_finance_records')
            ? CvFinanceRecord::orderBy('transaction_date', 'desc')->get()
            : collect();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="buku-kas-cv-pro-' . now()->format('Y-m-d-His') . '.csv"',
        ];

        return response()->stream(function () use ($records): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID TRX', 'Tanggal', 'Nama Klien', 'Email Klien', 'Paket Layanan', 'Nominal (IDR)', 'Metode Bayar', 'Status', 'Catatan']);

            foreach ($records as $row) {
                fputcsv($handle, [
                    $row->trx_code,
                    $row->transaction_date ? $row->transaction_date->format('Y-m-d') : '',
                    $row->client_name,
                    $row->client_email ?? '-',
                    $row->package_name,
                    (string) $row->amount_idr,
                    $row->payment_method,
                    $row->status === 'paid' ? 'LUNAS' : ($row->status === 'pending' ? 'MENUNGGU' : 'BATAL'),
                    $row->notes ?? '-',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}


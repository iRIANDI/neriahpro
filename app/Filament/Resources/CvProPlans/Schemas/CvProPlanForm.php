<?php

namespace App\Filament\Resources\CvProPlans\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class CvProPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identifikasi & Tipe Paket')
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode Unik Paket')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->placeholder('contoh: pro_career, topup_tailor_5'),

                        Select::make('type')
                            ->label('Tipe Paket')
                            ->options([
                                'subscription' => 'Langganan Berkala (Subscription)',
                                'topup' => 'A La Carte / Top-Up Tambahan',
                            ])
                            ->required()
                            ->default('subscription'),

                        TextInput::make('badge')
                            ->label('Badge Promosi')
                            ->placeholder('contoh: POPULER, BEST VALUE, HEMAT 40%')
                            ->maxLength(30),

                        Select::make('billing_cycle')
                            ->label('Siklus Tagihan')
                            ->options([
                                'monthly' => 'Bulanan (Monthly)',
                                'quarterly' => '3 Bulanan (Quarterly)',
                                'yearly' => 'Tahunan (Yearly)',
                                'one_time' => 'Sekali Beli (A La Carte / Lifetime Quota)',
                            ])
                            ->required()
                            ->default('monthly'),

                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),

                        TextInput::make('sort_order')
                            ->label('Urutan Tampilan')
                            ->numeric()
                            ->default(0),
                    ])->columns(3),

                Section::make('Penetapan Harga Dinamis')
                    ->description('Tentukan harga jual. Analisis margin keuntungan dan titik aman API akan dihitung otomatis secara real-time.')
                    ->schema([
                        TextInput::make('price_idr')
                            ->label('Harga (IDR)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->default(0),

                        TextInput::make('price_usd')
                            ->label('Harga (USD)')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->default(0),
                    ])->columns(2),

                Section::make('Alokasi Kuota Fitur AI (Isi -1 untuk Unlimited)')
                    ->schema([
                        TextInput::make('quotas.tailor_cv_limit')
                            ->label('Kuota AI Job Tailor CV')
                            ->numeric()
                            ->default(15)
                            ->helperText('Jumlah penyesuaian CV terhadap lowongan kerja.'),

                        TextInput::make('quotas.mock_interviews_limit')
                            ->label('Kuota Mock Interview Suara')
                            ->numeric()
                            ->default(5)
                            ->helperText('Jumlah sesi latihan wawancara rekaman suara.'),

                        TextInput::make('quotas.ats_audits_limit')
                            ->label('Kuota Audit Skor ATS')
                            ->numeric()
                            ->default(-1)
                            ->helperText('Pengecekan kualitas kata kerja & metrik (-1 = Unlimited).'),

                        TextInput::make('quotas.linkedin_packs_limit')
                            ->label('Kuota LinkedIn Branding Pack')
                            ->numeric()
                            ->default(5)
                            ->helperText('Headline, About, dan Post generator LinkedIn.'),

                        TextInput::make('quotas.outreach_letters_limit')
                            ->label('Kuota Surat Korespondensi')
                            ->numeric()
                            ->default(10)
                            ->helperText('Thank you letter, follow up, dan cold pitch.'),

                        TextInput::make('quotas.ai_credits')
                            ->label('Bonus Universal AI Credits')
                            ->numeric()
                            ->default(100)
                            ->helperText('Poin kredit fleksibel sebagai cadangan jika kuota habis.'),
                    ])->columns(3),

                Section::make('Nama & Deskripsi Multi-Bahasa')
                    ->schema([
                        Tabs::make('Languages')
                            ->tabs([
                                Tabs\Tab::make('Bahasa Indonesia')
                                    ->schema([
                                        TextInput::make('name.id')
                                            ->label('Nama Paket (ID)')
                                            ->required(),
                                        Textarea::make('description.id')
                                            ->label('Deskripsi Paket (ID)')
                                            ->rows(2),
                                    ]),
                                Tabs\Tab::make('English')
                                    ->schema([
                                        TextInput::make('name.en')
                                            ->label('Plan Name (EN)')
                                            ->required(),
                                        Textarea::make('description.en')
                                            ->label('Plan Description (EN)')
                                            ->rows(2),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\DomainHostingAssets\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;

class DomainHostingAssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Layanan & Aset')
                    ->description('Rincian identitas domain, hosting, atau server yang dikelola.')
                    ->schema([
                        Select::make('asset_type')
                            ->label('Tipe Aset')
                            ->options([
                                'domain' => 'Domain Name',
                                'hosting' => 'Web Hosting (Shared / Cloud)',
                                'vps' => 'VPS / Cloud Server Dedicated',
                                'ssl' => 'Sertifikat SSL Tambahan',
                                'email' => 'Business Email / Workspace',
                                'bundle' => 'Bundle (Domain + Hosting)',
                            ])
                            ->required()
                            ->default('domain'),

                        TextInput::make('name')
                            ->label('Nama / Label Layanan')
                            ->placeholder('e.g. Domain Utama NeriahPro, VPS Produksi Hetzner')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('domain_name')
                            ->label('Nama Domain / Hostname')
                            ->placeholder('contohbisnis.com')
                            ->maxLength(255),

                        TextInput::make('provider')
                            ->label('Provider / Registrar')
                            ->placeholder('e.g. Niagahoster, DomaiNesia, Cloudflare, Hetzner')
                            ->datalist([
                                'Niagahoster',
                                'DomaiNesia',
                                'Cloudflare',
                                'Namecheap',
                                'Hetzner',
                                'DigitalOcean',
                                'AWS',
                                'Google Cloud',
                                'IDCloudHost',
                                'Biznet Gio',
                                'Rumahweb',
                                'Dewaweb',
                                'Jagoan Hosting',
                            ])
                            ->required()
                            ->maxLength(255),

                        Select::make('vision_blueprint_id')
                            ->label('Tautkan ke Proyek Klien (Project OS)')
                            ->relationship('visionBlueprint', 'nama_bisnis')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Hubungkan ke Blueprint Proyek untuk integrasi otomatis ke kontrak & invoice klien.'),

                        Select::make('status')
                            ->label('Status Langganan')
                            ->options([
                                'active' => 'Aktif (Berjalan)',
                                'expiring_soon' => 'Mendekati Jatuh Tempo',
                                'expired' => 'Kadaluarsa',
                                'cancelled' => 'Dibatalkan / Dihentikan',
                                'transferred' => 'Ditransfer ke Klien',
                            ])
                            ->required()
                            ->default('active'),
                    ])->columns(2),

                Section::make('Siklus Sewa & Tanggal Jatuh Tempo')
                    ->description('Jadwal berlangganan dan batas waktu perpanjangan.')
                    ->schema([
                        DatePicker::make('purchase_date')
                            ->label('Tanggal Pembelian / Registrasi')
                            ->required()
                            ->default(now()),

                        DatePicker::make('expires_at')
                            ->label('Tanggal Jatuh Tempo / Kadaluarsa')
                            ->required()
                            ->helperText('Sistem otomatis mendeteksi tanggal ini untuk memicu pengingat berkala.'),

                        Select::make('billing_cycle')
                            ->label('Siklus Tagihan')
                            ->options([
                                'monthly' => 'Bulanan (1 Bulan)',
                                'quarterly' => 'Per 3 Bulan',
                                'semi_annual' => 'Per 6 Bulan',
                                'yearly' => 'Tahunan (1 Tahun)',
                                '2_years' => 'Per 2 Tahun',
                                '3_years' => 'Per 3 Tahun',
                            ])
                            ->required()
                            ->default('yearly'),

                        Toggle::make('auto_renew')
                            ->label('Auto-Renewal Aktif di Provider')
                            ->helperText('Aktifkan jika provider melakukan autodebit berkala.')
                            ->default(false),

                        TextInput::make('reminder_days_before')
                            ->label('Peringatkan H- Berapa Hari?')
                            ->numeric()
                            ->default(30)
                            ->suffix('Hari Sebelum')
                            ->helperText('Batas hari sebelum jatuh tempo untuk memicu notifikasi peringatan.'),
                    ])->columns(2),

                Section::make('Finansial & Tagihan')
                    ->schema([
                        TextInput::make('cost_price')
                            ->label('Biaya Beli / Modal Sewa (HPP)')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->helperText('Biaya riil yang dibayar ke provider.'),

                        TextInput::make('client_price')
                            ->label('Harga Tagihan ke Klien')
                            ->numeric()
                            ->prefix('Rp')
                            ->nullable()
                            ->helperText('Harga perpanjangan yang ditagihkan ke klien (jika dikelola agency).'),

                        Select::make('currency')
                            ->label('Mata Uang')
                            ->options([
                                'IDR' => 'IDR (Rupiah)',
                                'USD' => 'USD (Dollar AS)',
                                'EUR' => 'EUR (Euro)',
                                'SGD' => 'SGD (Sing Dollar)',
                            ])
                            ->default('IDR')
                            ->required(),
                    ])->columns(3),

                Section::make('Detail Teknis & Server')
                    ->collapsed()
                    ->schema([
                        TextInput::make('server_ip')
                            ->label('IP Server / Server IP Address')
                            ->placeholder('e.g. 103.123.45.67'),

                        TextInput::make('panel_url')
                            ->label('Tautan Panel Kontrol')
                            ->url()
                            ->placeholder('https://dash.cloudflare.com atau cPanel URL'),

                        Textarea::make('admin_notes')
                            ->label('Catatan Rahasia / DNS Nameserver')
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder("Nameserver 1: ns1.cloudflare.com\nNameserver 2: ns2.cloudflare.com\nSpesifikasi: 4 vCPU, 8GB RAM, 160GB NVMe"),
                    ])->columns(2),
            ]);
    }
}

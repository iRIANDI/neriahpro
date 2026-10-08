<?php

namespace App\Filament\Resources\EmailTemplates\Schemas;

use App\Support\FilamentRichEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class EmailTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Template Email')
                    ->description('Kode identitas unik dan status keaktifan template email sistem.')
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode Unik Template (System Key)')
                            ->placeholder('e.g. customer_otp, client_welcome, invoice_receipt')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100)
                            ->helperText('Digunakan oleh backend untuk memanggil template ini secara terprogram (contoh: customer_otp).'),

                        TextInput::make('name')
                            ->label('Nama Template (Deskriptif)')
                            ->placeholder('e.g. Template OTP Autentikasi Klien (2-FA)')
                            ->required()
                            ->maxLength(255),

                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->helperText('Jika non-aktif, sistem akan beralih ke template fallback bawaan.'),
                    ])->columns(3),

                Section::make('Identitas Pengirim & Routing (Sender & Reply-To)')
                    ->description('Alamat pengirim resmi dan tujuan balasan email untuk template ini.')
                    ->schema([
                        TextInput::make('sender_name')
                            ->label('Nama Pengirim (Display Name)')
                            ->default('Neriah Pro Support')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('sender_email')
                            ->label('Email Pengirim (From Address)')
                            ->default('support@neriahpro.com')
                            ->email()
                            ->required()
                            ->helperText('Email pengirim resmi pada domain neriahpro.com.'),

                        TextInput::make('reply_to_email')
                            ->label('Email Balasan (Reply-To Header)')
                            ->default('support@neriahpro.com')
                            ->email()
                            ->required()
                            ->helperText('Ketika penerima menekan "Balas", email akan dikirim ke alamat ini.'),
                    ])->columns(3),

                Section::make('Konten & Layout Email')
                    ->description('Subjek dan struktur HTML template. Gunakan tag variabel yang tersedia.')
                    ->schema([
                        TextInput::make('subject')
                            ->label('Subjek Email')
                            ->placeholder('e.g. [Neriah Pro] {{otp}} adalah Kode OTP Masuk Anda')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Dapat menyertakan variabel, misalnya: {{otp}}, {{email}}, {{name}}.'),

                        Placeholder::make('variable_guide')
                            ->label('Daftar Variabel / Placeholders yang Didukung')
                            ->content(fn ($record) => new HtmlString('
                                <div class="p-3 bg-zinc-900 border border-zinc-700 text-xs font-mono text-zinc-300 space-y-1.5 rounded-none">
                                    <div class="font-bold text-emerald-400">Variabel Khusus OTP Klien:</div>
                                    <div><code class="text-amber-300">{{otp}}</code> : 6-digit kode OTP keamanan</div>
                                    <div><code class="text-amber-300">{{email}}</code> : Alamat email penerima</div>
                                    <div><code class="text-amber-300">{{ip_address}}</code> : IP Address peminta kode OTP</div>
                                    <div><code class="text-amber-300">{{expiry_minutes}}</code> : Masa aktif kode OTP (default: 10 menit)</div>
                                    <div><code class="text-amber-300">{{requested_at}}</code> : Waktu pengiriman OTP (WIB)</div>
                                    <div class="pt-1.5 border-t border-zinc-800 font-bold text-emerald-400">Variabel Global Sistem:</div>
                                    <div><code class="text-amber-300">{{support_email}}</code> : support@neriahpro.com</div>
                                    <div><code class="text-amber-300">{{app_name}}</code> : Neriah Pro</div>
                                    <div><code class="text-amber-300">{{app_url}}</code> : https://neriahpro.com</div>
                                </div>
                            ')),

                        Textarea::make('body_html')
                            ->label('Template HTML Body (Desain Email)')
                            ->required()
                            ->rows(18)
                            ->extraAttributes(['class' => 'font-mono text-xs leading-relaxed'])
                            ->helperText('Mendukung markup HTML email standar dengan inline style untuk kompatibilitas Gmail, Apple Mail, dan Outlook.'),
                    ]),
            ]);
    }
}

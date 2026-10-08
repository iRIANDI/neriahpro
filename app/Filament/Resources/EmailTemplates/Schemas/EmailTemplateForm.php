<?php

namespace App\Filament\Resources\EmailTemplates\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Tabs;
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

                Section::make('Panduan Variabel & Desain Adaptif Tema')
                    ->description('Daftar placeholder data dinamis dan token warna tema adaptif (Light / Dark Mode).')
                    ->schema([
                        Placeholder::make('variable_guide')
                            ->label('Token Placeholders yang Didukung')
                            ->content(fn () => new HtmlString('
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs font-mono">
                                    <div class="p-3 bg-zinc-900 border border-zinc-700 text-zinc-300 space-y-1 rounded-none">
                                        <div class="font-bold text-emerald-400 uppercase tracking-wider mb-1">&bull; Variabel Data Transaksional:</div>
                                        <div><code class="text-amber-300">{{otp}}</code> : 6-digit kode OTP keamanan</div>
                                        <div><code class="text-amber-300">{{email}}</code> : Alamat email penerima</div>
                                        <div><code class="text-amber-300">{{name}}</code> : Nama pengguna / klien</div>
                                        <div><code class="text-amber-300">{{ip_address}}</code> : IP Address peminta</div>
                                        <div><code class="text-amber-300">{{expiry_minutes}}</code> : Masa aktif OTP (menit)</div>
                                        <div><code class="text-amber-300">{{requested_at}}</code> : Waktu pengiriman (WIB)</div>
                                        <div><code class="text-amber-300">{{support_email}}</code> : support@neriahpro.com</div>
                                        <div><code class="text-amber-300">{{app_url}}</code> : https://neriahpro.com</div>
                                    </div>
                                    <div class="p-3 bg-zinc-900 border border-zinc-700 text-zinc-300 space-y-1 rounded-none">
                                        <div class="font-bold text-sky-400 uppercase tracking-wider mb-1">&bull; Token Tema Adaptif (Light/Dark):</div>
                                        <div><code class="text-sky-300">{{theme_body_bg}}</code> : Background luar email</div>
                                        <div><code class="text-sky-300">{{theme_card_bg}}</code> : Background kartu konten</div>
                                        <div><code class="text-sky-300">{{theme_card_border}}</code> : Border kartu</div>
                                        <div><code class="text-sky-300">{{theme_header_bg}}</code> : Background header</div>
                                        <div><code class="text-sky-300">{{theme_title_color}}</code> : Warna teks judul</div>
                                        <div><code class="text-sky-300">{{theme_body_text}}</code> : Warna teks paragraf</div>
                                        <div><code class="text-sky-300">{{theme_otp_color}}</code> : Warna angka kode OTP</div>
                                        <div><code class="text-sky-300">{{theme_box_bg}}</code> : Kotak kode OTP</div>
                                        <div><code class="text-sky-300">{{theme_alert_bg}}</code> : Kotak peringatan keamanan</div>
                                    </div>
                                </div>
                            ')),
                    ]),

                Section::make('Konten Multibahasa Email (Bilingual Tabs: ID & EN)')
                    ->description('Kelola subjek dan struktur HTML template untuk Bahasa Indonesia dan Bahasa Inggris secara terpisah.')
                    ->schema([
                        Tabs::make('Language Selector')
                            ->tabs([
                                Tabs\Tab::make('Bahasa Indonesia')
                                    ->icon('heroicon-m-language')
                                    ->badge('ID')
                                    ->schema([
                                        TextInput::make('subject.id')
                                            ->label('Subjek Email (Bahasa Indonesia)')
                                            ->placeholder('e.g. [Neriah Pro] {{otp}} adalah Kode OTP Masuk Anda')
                                            ->required()
                                            ->maxLength(255)
                                            ->helperText('Subjek email dalam Bahasa Indonesia.'),

                                        Textarea::make('body_html.id')
                                            ->label('Template HTML Body (Bahasa Indonesia)')
                                            ->required()
                                            ->rows(18)
                                            ->extraAttributes(['class' => 'font-mono text-xs leading-relaxed'])
                                            ->helperText('Markup HTML email standar dengan inline styles dan token tema untuk Bahasa Indonesia.'),
                                    ]),

                                Tabs\Tab::make('English')
                                    ->icon('heroicon-m-globe-alt')
                                    ->badge('EN')
                                    ->schema([
                                        TextInput::make('subject.en')
                                            ->label('Email Subject (English)')
                                            ->placeholder('e.g. [Neriah Pro] {{otp}} is Your Login OTP Code')
                                            ->required()
                                            ->maxLength(255)
                                            ->helperText('Email subject in English (used whenever the client selects English or non-Indonesian locale).'),

                                        Textarea::make('body_html.en')
                                            ->label('HTML Body Template (English)')
                                            ->required()
                                            ->rows(18)
                                            ->extraAttributes(['class' => 'font-mono text-xs leading-relaxed'])
                                            ->helperText('Standard email HTML markup with inline styles and theme tokens for English.'),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

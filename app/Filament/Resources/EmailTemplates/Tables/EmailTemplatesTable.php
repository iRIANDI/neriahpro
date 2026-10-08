<?php

namespace App\Filament\Resources\EmailTemplates\Tables;

use App\Models\EmailTemplate;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class EmailTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode Key')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('name')
                    ->label('Nama Template')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('subject')
                    ->label('Subjek Email (ID / EN)')
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            $id = $state['id'] ?? '';
                            $en = $state['en'] ?? '';
                            return "ID: {$id} | EN: {$en}";
                        }
                        return (string) $state;
                    })
                    ->limit(60)
                    ->searchable(),

                TextColumn::make('sender_email')
                    ->label('Pengirim')
                    ->description(fn (EmailTemplate $record): string => $record->sender_name ?: 'Neriah Pro'),

                IconColumn::make('is_active')
                    ->label('Status Aktif')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                // Interactive Multilingual & Multitheme Preview Action
                Action::make('preview')
                    ->label('Pratinjau')
                    ->icon('heroicon-o-eye')
                    ->color('secondary')
                    ->modalHeading(fn (EmailTemplate $record) => "Pratinjau Email Multibahasa & Tema: {$record->name}")
                    ->modalWidth('5xl')
                    ->modalContent(function (EmailTemplate $record) {
                        $demoVars = [
                            'otp' => '589124',
                            'email' => auth()->user()?->email ?: 'client@startup.co.id',
                            'ip_address' => request()->ip() ?: '127.0.0.1',
                            'requested_at' => now()->timezone('Asia/Jakarta')->format('d M Y, H:i:s') . ' WIB',
                            'expiry_minutes' => '10',
                            'name' => auth()->user()?->name ?: 'Budi Santoso',
                        ];

                        $variants = [
                            'id_dark' => EmailTemplate::renderTemplate($record->code, $demoVars, 'id', 'dark'),
                            'id_light' => EmailTemplate::renderTemplate($record->code, $demoVars, 'id', 'light'),
                            'en_dark' => EmailTemplate::renderTemplate($record->code, $demoVars, 'en', 'dark'),
                            'en_light' => EmailTemplate::renderTemplate($record->code, $demoVars, 'en', 'light'),
                        ];

                        $html = '
                        <div x-data="{ activeLang: \'id\', activeTheme: \'dark\' }" class="space-y-4 font-mono text-xs">
                            <!-- Language & Theme Switcher Bar -->
                            <div class="flex flex-wrap items-center justify-between gap-3 p-3 bg-zinc-900 border border-zinc-700 rounded-none">
                                <div class="flex items-center gap-2">
                                    <span class="text-zinc-400 font-bold uppercase tracking-wider text-[11px]">BAHASA:</span>
                                    <button type="button" @click="activeLang = \'id\'" :class="activeLang === \'id\' ? \'bg-emerald-500 text-black font-bold\' : \'bg-zinc-800 text-zinc-300 hover:text-white\'" class="px-2.5 py-1 rounded-none text-xs transition cursor-pointer">
                                        🇮🇩 ID (Indonesia)
                                    </button>
                                    <button type="button" @click="activeLang = \'en\'" :class="activeLang === \'en\' ? \'bg-emerald-500 text-black font-bold\' : \'bg-zinc-800 text-zinc-300 hover:text-white\'" class="px-2.5 py-1 rounded-none text-xs transition cursor-pointer">
                                        🇬🇧 EN (English)
                                    </button>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-zinc-400 font-bold uppercase tracking-wider text-[11px]">TEMA TAMPILAN:</span>
                                    <button type="button" @click="activeTheme = \'dark\'" :class="activeTheme === \'dark\' ? \'bg-indigo-600 text-white font-bold shadow-xs\' : \'bg-zinc-800 text-zinc-300 hover:text-white\'" class="px-2.5 py-1 rounded-none text-xs transition cursor-pointer">
                                        🌙 Dark Mode
                                    </button>
                                    <button type="button" @click="activeTheme = \'light\'" :class="activeTheme === \'light\' ? \'bg-amber-400 text-black font-bold shadow-xs\' : \'bg-zinc-800 text-zinc-300 hover:text-white\'" class="px-2.5 py-1 rounded-none text-xs transition cursor-pointer">
                                        ☀️ Light Mode
                                    </button>
                                </div>
                            </div>
                        ';

                        foreach ($variants as $key => $v) {
                            [$langKey, $themeKey] = explode('_', $key);
                            $bgPreview = $themeKey === 'dark' ? '#09090b' : '#f4f4f5';
                            $html .= '
                            <div x-show="activeLang === \'' . $langKey . '\' && activeTheme === \'' . $themeKey . '\'" class="space-y-3" x-cloak>
                                <div class="p-3 bg-zinc-900 border border-zinc-700 text-zinc-300 space-y-1 rounded-none">
                                    <div><strong class="text-emerald-400">Subjek (' . strtoupper($langKey) . '):</strong> ' . htmlspecialchars($v['subject']) . '</div>
                                    <div><strong class="text-emerald-400">Dari:</strong> ' . htmlspecialchars($v['sender_name']) . ' &lt;' . htmlspecialchars($v['sender_email']) . '&gt;</div>
                                    <div><strong class="text-emerald-400">Reply-To:</strong> ' . htmlspecialchars($v['reply_to_email']) . '</div>
                                </div>
                                <div class="border border-zinc-700 rounded-none overflow-hidden p-2" style="background-color: ' . $bgPreview . ';">
                                    <iframe srcdoc="' . htmlspecialchars($v['body_html']) . '" class="w-full h-[520px] border-0" style="background-color: ' . $bgPreview . ';"></iframe>
                                </div>
                            </div>
                            ';
                        }

                        $html .= '</div>';
                        return new HtmlString($html);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup Pratinjau'),

                EditAction::make(),

                DeleteAction::make()
                    ->hidden(fn (EmailTemplate $record) => in_array($record->code, ['customer_otp', 'client_welcome'])),
            ]);
    }
}

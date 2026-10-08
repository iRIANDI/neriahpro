<?php

namespace App\Filament\Resources\EmailTemplates\Tables;

use App\Models\EmailTemplate;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;
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
                    ->label('Subjek Email')
                    ->limit(40)
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
                // Preview Action
                Action::make('preview')
                    ->label('Pratinjau')
                    ->icon('heroicon-o-eye')
                    ->color('secondary')
                    ->modalHeading(fn (EmailTemplate $record) => "Pratinjau Email: {$record->name}")
                    ->modalWidth('4xl')
                    ->modalContent(function (EmailTemplate $record) {
                        $rendered = EmailTemplate::renderTemplate($record->code, [
                            'otp' => '589124',
                            'email' => auth()->user()?->email ?: 'client@startup.co.id',
                            'ip_address' => request()->ip() ?: '127.0.0.1',
                            'requested_at' => now()->timezone('Asia/Jakarta')->format('d M Y, H:i:s') . ' WIB',
                            'expiry_minutes' => '10',
                            'name' => auth()->user()?->name ?: 'Budi Santoso',
                        ]);

                        return new HtmlString('
                            <div class="space-y-3 font-mono text-xs">
                                <div class="p-3 bg-zinc-900 border border-zinc-700 text-zinc-300 space-y-1">
                                    <div><strong class="text-emerald-400">Subjek:</strong> ' . htmlspecialchars($rendered['subject']) . '</div>
                                    <div><strong class="text-emerald-400">Dari:</strong> ' . htmlspecialchars($rendered['sender_name']) . ' &lt;' . htmlspecialchars($rendered['sender_email']) . '&gt;</div>
                                    <div><strong class="text-emerald-400">Reply-To:</strong> ' . htmlspecialchars($rendered['reply_to_email']) . '</div>
                                </div>
                                <div class="border border-zinc-700 rounded-none overflow-hidden bg-black p-2">
                                    <iframe srcdoc="' . htmlspecialchars($rendered['body_html']) . '" class="w-full h-[520px] border-0 bg-[#09090b]"></iframe>
                                </div>
                            </div>
                        ');
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup Pratinjau'),

                EditAction::make(),

                DeleteAction::make()
                    ->hidden(fn (EmailTemplate $record) => in_array($record->code, ['customer_otp', 'client_welcome'])),
            ]);
    }
}

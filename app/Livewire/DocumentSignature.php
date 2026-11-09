<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Document;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;

class DocumentSignature extends Component implements HasForms
{
    use InteractsWithForms;

    public Document $document;
    public ?array $data = [];
    public bool $isSigned = false;

    public function mount(Document $document)
    {
        $this->document = $document;

        // Auto-heal: If document was recorded as signed but signature image is empty or corrupt (<500 bytes), reset to draft so user can draw properly
        $sigImage = $this->document->digital_signature_image;
        $isCorruptOrBlank = empty($sigImage) || (is_string($sigImage) && strlen($sigImage) < 500);

        if ($this->document->status === 'signed' && $isCorruptOrBlank) {
            $this->document->update([
                'status' => 'draft',
                'digital_signature_image' => null,
                'signed_at' => null,
                'scope_locked' => false,
            ]);
            $this->isSigned = false;
        } else {
            $this->isSigned = $this->document->status === 'signed';
        }

        if (!$this->isSigned) {
            $this->form->fill();
        }
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Tanda Tangan')
                    ->description('Silakan berikan tanda tangan digital Anda pada kotak di bawah ini.')
                    ->schema([
                        SignaturePad::make('digital_signature_image')
                            ->hiddenLabel()
                            ->dotSize(2.0)
                            ->lineMinWidth(1.0)
                            ->lineMaxWidth(2.5)
                            ->penColor('blue')
                            ->backgroundColor('rgba(255, 255, 255, 1)') // solid white background for client signature
                            ->clearable()
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(1) // Fixes pointer offset by enforcing 1 column
            ])
            ->statePath('data');
    }

    public function unlockForResign(): void
    {
        $blueprint = $this->document->related instanceof \App\Models\VisionBlueprint ? $this->document->related : null;
        if ($blueprint && in_array($blueprint->project_status, ['Active Sprint', 'In Review', 'Completed'])) {
            $this->dispatch('signature-error', message: 'Kontrak yang sudah berjalan aktif dalam masa sprint tidak dapat diubah kembali.');
            return;
        }

        $this->document->update([
            'status' => 'draft',
            'digital_signature_image' => null,
            'signed_at' => null,
            'scope_locked' => false,
        ]);

        $this->isSigned = false;
        $this->form->fill();
    }

    public function submitSignature(): void
    {
        $data = $this->form->getState();
        $sigImage = $data['digital_signature_image'] ?? null;

        // Strict validation: Reject empty or blank canvas submissions
        if (empty($sigImage) || !is_string($sigImage) || strlen($sigImage) < 500) {
            $this->dispatch('signature-error', message: 'Tanda tangan belum dibubuhkan! Silakan goreskan tanda tangan Anda pada kotak yang tersedia terlebih dahulu.');
            return;
        }

        $this->document->update([
            'digital_signature_image' => $sigImage,
            'signed_at' => now(), // Stored in UTC (standard)
            'signer_ip_address' => request()->ip(),
            'status' => 'signed',
            'scope_locked' => true,
        ]);

        if ($this->document->related instanceof \App\Models\VisionBlueprint) {
            $this->document->related->recordSignOff(request()->ip(), request()->userAgent());
        }

        $this->isSigned = true;
    }

    public function render()
    {
        return view('livewire.document-signature')
            ->layout('components.layouts.app', ['title' => 'Tanda Tangan Dokumen']);
    }
}

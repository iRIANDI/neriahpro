<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Document;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;
use Filament\Forms\Components\Section;

class DocumentSignature extends Component implements HasForms
{
    use InteractsWithForms;

    public Document $document;
    public ?array $data = [];
    public bool $isSigned = false;

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->isSigned = $this->document->status === 'signed';

        if (!$this->isSigned) {
            $this->form->fill();
        }
    }

    public function form(Form $form): Form
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

    public function submitSignature()
    {
        $data = $this->form->getState();

        $this->document->update([
            'digital_signature_image' => $data['digital_signature_image'],
            'signed_at' => now(), // Stored in UTC (standard)
            'signer_ip_address' => request()->ip(),
            'status' => 'signed'
        ]);

        $this->isSigned = true;
    }

    public function render()
    {
        return view('livewire.document-signature')
            ->layout('layouts.app', ['title' => 'Tanda Tangan Dokumen']);
    }
}

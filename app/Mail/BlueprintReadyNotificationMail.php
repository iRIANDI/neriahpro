<?php

namespace App\Mail;

use App\Models\VisionBlueprint;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BlueprintReadyNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public VisionBlueprint $blueprint,
        public string $tierName = 'Pro',
        public ?string $accessUrl = null
    ) {
        $this->accessUrl = $accessUrl ?: url('/blueprint/' . $blueprint->slug);
    }

    public function envelope(): Envelope
    {
        $projectName = $this->blueprint->nama_bisnis ?: $this->blueprint->client_name ?: 'Proyek Anda';
        return new Envelope(
            subject: "[Neriah Pro] Cetak Biru Arsitektur Siap: {$projectName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.blueprint-ready',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

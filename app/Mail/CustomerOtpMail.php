<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $renderedTemplate;

    public function __construct(
        public string $otp,
        public string $email,
        public ?string $ipAddress = null,
        public int $expiryMinutes = 10,
        $locale = 'id',
        $theme = 'dark'
    ) {
        $this->locale = $locale ?: 'id';
        $this->theme = $theme ?: 'dark';
        $this->renderedTemplate = EmailTemplate::renderTemplate('customer_otp', [
            'otp' => $otp,
            'email' => $email,
            'ip_address' => $ipAddress ?: '127.0.0.1',
            'expiry_minutes' => (string) $expiryMinutes,
            'requested_at' => now()->timezone('Asia/Jakarta')->format('d M Y, H:i:s') . ' WIB',
            'support_email' => 'support@neriahpro.com',
        ], locale: (string) $this->locale, theme: (string) $this->theme);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->renderedTemplate['sender_email'], $this->renderedTemplate['sender_name']),
            replyTo: [new Address($this->renderedTemplate['reply_to_email'], $this->renderedTemplate['sender_name'])],
            subject: $this->renderedTemplate['subject'],
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: $this->renderedTemplate['body_html']
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

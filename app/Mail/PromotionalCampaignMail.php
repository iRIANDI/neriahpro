<?php

namespace App\Mail;

use App\Models\EmailCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PromotionalCampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $renderedBody;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public EmailCampaign $campaign,
        public string $recipientName = 'Klien',
        public string $recipientEmail = ''
    ) {
        // Personalize content
        $content = $campaign->content_html;
        $content = str_replace(
            ['{name}', '{nama}', '{email}', '{cta_url}'],
            [$this->recipientName, $this->recipientName, $this->recipientEmail, $campaign->cta_url ?: url('/')],
            $content
        );

        $this->renderedBody = $content;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $senderEmail = $this->campaign->sender_email ?: config('mail.from.address', 'support@neriahpro.com');
        $senderName = $this->campaign->sender_name ?: config('mail.from.name', 'Neriah Pro');

        $replyToEmail = $this->campaign->reply_to_email ?: 'yoseph.iriandi.tambunan@gmail.com';
        $replyToName = $this->campaign->reply_to_name ?: 'Yoseph Iriandi';

        return new Envelope(
            from: new Address($senderEmail, $senderName),
            replyTo: [
                new Address($replyToEmail, $replyToName),
            ],
            subject: $this->campaign->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.promotional-campaign',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

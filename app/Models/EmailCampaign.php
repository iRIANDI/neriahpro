<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class EmailCampaign extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'created_by_user_id',
        'title',
        'subject',
        'preview_text',
        'sender_name',
        'sender_email',
        'reply_to_email',
        'reply_to_name',
        'target_audience',
        'custom_recipient_email',
        'custom_recipient_name',
        'custom_company_name',
        'content_html',
        'cta_label',
        'cta_url',
        'status',
        'total_recipients',
        'sent_count',
        'failed_count',
        'scheduled_at',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
            'total_recipients' => 'integer',
            'sent_count' => 'integer',
            'failed_count' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(EmailCampaignLog::class);
    }

    /**
     * Resolve unique recipients according to target audience segment
     *
     * @return Collection<int, array{email: string, name: string}>
     */
    public function resolveRecipients(): Collection
    {
        $recipients = collect();

        // 1. Manual Single Recipient
        if ($this->target_audience === 'manual_recipient' || (!empty($this->custom_recipient_email) && $this->target_audience !== 'all')) {
            if (!empty($this->custom_recipient_email)) {
                $recipients->push([
                    'email' => strtolower(trim($this->custom_recipient_email)),
                    'name' => $this->custom_recipient_name ?: ($this->custom_company_name ?: 'Klien'),
                ]);
            }
            if ($this->target_audience === 'manual_recipient') {
                return $recipients->unique('email')->values();
            }
        }

        // 2. CRM Lead Contacts Database
        if (in_array($this->target_audience, ['all', 'lead_contacts'])) {
            $leads = LeadContact::query()
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->get(['email', 'name', 'company_name']);

            foreach ($leads as $lead) {
                $recipients->push([
                    'email' => strtolower(trim($lead->email)),
                    'name' => $lead->name ?: ($lead->company_name ?: 'Partner'),
                ]);
            }
        }

        // 3. Client Onboarding Leads
        if (in_array($this->target_audience, ['all', 'onboarding_clients'])) {
            $onboardings = ClientOnboarding::query()
                ->whereNotNull('contact_email')
                ->where('contact_email', '!=', '')
                ->get(['contact_email', 'contact_name', 'company_name']);

            foreach ($onboardings as $item) {
                $recipients->push([
                    'email' => strtolower(trim($item->contact_email)),
                    'name' => $item->contact_name ?: ($item->company_name ?: 'Klien'),
                ]);
            }
        }

        // 4. Vision Blueprint (Project OS) Clients
        if (in_array($this->target_audience, ['all', 'blueprint_clients'])) {
            $blueprints = VisionBlueprint::query()
                ->whereNotNull('client_email')
                ->where('client_email', '!=', '')
                ->get(['client_email', 'client_name', 'company_name']);

            foreach ($blueprints as $item) {
                $recipients->push([
                    'email' => strtolower(trim($item->client_email)),
                    'name' => $item->client_name ?: ($item->company_name ?: 'Partner'),
                ]);
            }
        }

        // 5. Registered Users
        if (in_array($this->target_audience, ['all', 'cv_users'])) {
            $users = User::query()
                ->whereNotNull('email')
                ->get(['email', 'name']);

            foreach ($users as $user) {
                $recipients->push([
                    'email' => strtolower(trim($user->email)),
                    'name' => $user->name ?: 'Pengguna Neriah Pro',
                ]);
            }
        }

        // Unique by email
        return $recipients->unique('email')->values();
    }
}

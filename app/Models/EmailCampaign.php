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
        'target_audience',
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

        // 1. Client Onboarding Leads
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

        // 2. Vision Blueprint (Project OS) Clients
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

        // 3. Registered Users
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

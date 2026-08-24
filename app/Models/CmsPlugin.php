<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsPlugin extends Model
{
    use HasUlids;

    protected $fillable = [
        'cms_page_id',
        'plugin_type',
        'content_data',
        'order',
        'is_active',
    ];

    protected $casts = [
        'content_data' => 'array',
        'is_active' => 'boolean',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(CmsPage::class, 'cms_page_id');
    }
}

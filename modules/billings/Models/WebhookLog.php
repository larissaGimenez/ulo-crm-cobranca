<?php

namespace Modules\Billings\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    protected $fillable = [
        'provider',
        'payload',
        'processed',
        'error_message',
    ];

    protected $casts = [
        'payload' => 'array',
        'processed' => 'boolean',
    ];
}

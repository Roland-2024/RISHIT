<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['label', 'recipient_name', 'phone', 'street', 'city', 'postal_code'])]
class Address extends Model
{
    protected $attributes = [
        'country_code' => 'AL',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use App\Domain\Commerce\OrderState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class OrderTransition extends Model
{
    public $timestamps = false;

    protected $guarded = ['*'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Order transition history cannot be changed.'));
        static::deleting(fn () => throw new LogicException('Order transition history cannot be deleted.'));
    }

    protected function casts(): array
    {
        return [
            'from_state' => OrderState::class,
            'to_state' => OrderState::class,
            'occurred_at' => 'immutable_datetime',
        ];
    }
}

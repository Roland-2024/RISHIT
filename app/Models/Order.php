<?php

namespace App\Models;

use App\Domain\Commerce\BuyerFeePolicy;
use App\Domain\Commerce\OrderAmounts;
use App\Domain\Commerce\OrderState;
use App\Domain\Commerce\ReservationProfile;
use App\Domain\Shared\Currency;
use App\Domain\Shared\Money;
use DomainException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

class Order extends Model
{
    protected $guarded = ['*'];

    private const IMMUTABLE_ATTRIBUTES = [
        'listing_id',
        'buyer_id',
        'seller_id',
        'idempotency_key',
        'reservation_profile',
        'reservation_started_at',
        'reservation_expires_at',
        'currency',
        'item_amount',
        'shipping_amount',
        'buyer_fee_amount',
        'seller_fee_amount',
        'total_amount',
        'seller_payable_amount',
        'buyer_fee_policy_version',
        'fee_policy_snapshot',
        'item_snapshot',
        'buyer_snapshot',
        'seller_snapshot',
        'buyer_address_snapshot',
        'seller_address_snapshot',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function transitions(): HasMany
    {
        return $this->hasMany(OrderTransition::class)->orderBy('occurred_at')->orderBy('id');
    }

    protected static function booted(): void
    {
        static::creating(function (self $order): void {
            $amounts = OrderAmounts::calculate(
                new Money($order->item_amount, $order->currency),
                new Money($order->shipping_amount, $order->currency),
                $order->buyer_fee_policy_version,
            );

            if (
                $order->buyer_fee_amount !== $amounts->buyerFee
                || $order->seller_fee_amount !== $amounts->sellerFees()
                || $order->total_amount !== $amounts->total
                || $order->seller_payable_amount !== $amounts->sellerPayable
                || $order->fee_policy_snapshot !== $amounts->snapshot($order->buyer_fee_policy_version)
            ) {
                throw new DomainException('Order commercial totals or fee snapshots are inconsistent.');
            }
        });

        static::updating(function (self $order): void {
            if ($order->isDirty(self::IMMUTABLE_ATTRIBUTES)) {
                throw new LogicException('Order commercial snapshots cannot be changed.');
            }
        });

        static::deleting(fn () => throw new LogicException('Orders cannot be deleted.'));
    }

    protected function casts(): array
    {
        return [
            'state' => OrderState::class,
            'reservation_profile' => ReservationProfile::class,
            'inventory_claim' => 'boolean',
            'currency' => Currency::class,
            'item_amount' => 'integer',
            'shipping_amount' => 'integer',
            'buyer_fee_amount' => 'integer',
            'seller_fee_amount' => 'integer',
            'total_amount' => 'integer',
            'seller_payable_amount' => 'integer',
            'buyer_fee_policy_version' => BuyerFeePolicy::class,
            'fee_policy_snapshot' => 'array',
            'item_snapshot' => 'array',
            'buyer_snapshot' => 'array',
            'seller_snapshot' => 'array',
            'buyer_address_snapshot' => 'array',
            'seller_address_snapshot' => 'array',
            'state_changed_at' => 'immutable_datetime',
            'reservation_started_at' => 'immutable_datetime',
            'reservation_expires_at' => 'immutable_datetime',
        ];
    }
}

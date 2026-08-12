<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const COLUMNS = [
        'item_amount',
        'shipping_amount',
        'buyer_fee_amount',
        'seller_fee_amount',
        'total_amount',
        'seller_payable_amount',
    ];

    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            foreach (self::COLUMNS as $column) {
                $table->bigInteger($column)->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            foreach (self::COLUMNS as $column) {
                $table->unsignedBigInteger($column)->change();
            }
        });
    }
};

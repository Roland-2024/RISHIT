<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('idempotency_key', 64)->nullable()->after('seller_id');
            $table->string('reservation_profile', 64)->nullable()->after('state');
            $table->timestamp('reservation_started_at')->nullable()->after('reservation_profile');
            $table->timestamp('reservation_expires_at')->nullable()->after('reservation_started_at');
            $table->boolean('inventory_claim')->nullable()->default(true)->after('reservation_expires_at');
            $table->index('listing_id', 'orders_listing_lookup_index');
            $table->unique(['buyer_id', 'idempotency_key'], 'orders_buyer_idempotency_unique');
            $table->index(['inventory_claim', 'reservation_expires_at'], 'orders_reservation_expiry_index');
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->dropUnique('orders_listing_id_unique');
            $table->unique(['listing_id', 'inventory_claim'], 'orders_listing_claim_unique');
        });

        Schema::table('order_transitions', function (Blueprint $table): void {
            $table->string('reason', 64)->nullable()->after('to_state');
            $table->string('listing_status', 16)->nullable()->after('reason');
        });
    }

    public function down(): void
    {
        Schema::table('order_transitions', function (Blueprint $table): void {
            $table->dropColumn(['reason', 'listing_status']);
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->dropUnique('orders_listing_claim_unique');
            $table->unique('listing_id');
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->dropUnique('orders_buyer_idempotency_unique');
            $table->dropIndex('orders_reservation_expiry_index');
            $table->dropIndex('orders_listing_lookup_index');
            $table->dropColumn([
                'idempotency_key',
                'reservation_profile',
                'reservation_started_at',
                'reservation_expires_at',
                'inventory_claim',
            ]);
        });
    }
};

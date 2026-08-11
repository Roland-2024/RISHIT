<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('is_admin')->default(false)->after('preferred_currency');
        });

        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('listing_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('seller_id')->constrained('users')->restrictOnDelete();
            $table->string('state', 32)->index();
            $table->char('currency', 3);
            $table->unsignedBigInteger('item_amount');
            $table->unsignedBigInteger('shipping_amount');
            $table->unsignedBigInteger('buyer_fee_amount');
            $table->unsignedBigInteger('seller_fee_amount');
            $table->unsignedBigInteger('total_amount');
            $table->unsignedBigInteger('seller_payable_amount');
            $table->string('buyer_fee_policy_version', 64);
            $table->json('fee_policy_snapshot');
            $table->json('item_snapshot');
            $table->json('buyer_snapshot');
            $table->json('seller_snapshot');
            $table->json('buyer_address_snapshot');
            $table->json('seller_address_snapshot');
            $table->timestamp('state_changed_at');
            $table->timestamps();

            $table->index(['buyer_id', 'created_at']);
            $table->index(['seller_id', 'created_at']);
        });

        Schema::create('order_transitions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('from_state', 32)->nullable();
            $table->string('to_state', 32);
            $table->timestamp('occurred_at');

            $table->index(['order_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_transitions');
        Schema::dropIfExists('orders');

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('is_admin');
        });
    }
};

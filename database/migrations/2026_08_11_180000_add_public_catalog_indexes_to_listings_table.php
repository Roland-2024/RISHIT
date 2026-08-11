<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table): void {
            $table->index(['status', 'currency', 'deleted_at', 'created_at'], 'listings_public_created_index');
            $table->index(['status', 'currency', 'deleted_at', 'price_amount'], 'listings_public_price_index');
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table): void {
            $table->dropIndex('listings_public_created_index');
            $table->dropIndex('listings_public_price_index');
        });
    }
};

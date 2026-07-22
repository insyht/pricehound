<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_user', function (Blueprint $table) {
            $table->timestamp('checked_at')->nullable(true)->comment('When was the price for this product last checked by the Hound? This is regardless of whether the price has changed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_user', function (Blueprint $table) {
            $table->dropColumn('checked_at');
        });
    }
};

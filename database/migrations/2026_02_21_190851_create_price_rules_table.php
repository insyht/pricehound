<?php

use App\Enums\PriceRuleTypes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_user_id');
            $table->enum('type', array_column(PriceRuleTypes::cases(), 'value'));
            $table->string('value');
            $table->timestamps();

            $table->foreign('product_user_id')->references('id')->on('product_user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_rules');
    }
};

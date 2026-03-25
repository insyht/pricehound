<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('identifier')->nullable()->default(null)->index();
            $table->unique(['identifier', 'created_by_user_id']);
        });
        foreach (Product::all() as $product) {
            $product->update(['identifier' => $product->ean]);
        }
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique('products_ean_unique');
            $table->dropColumn('ean');
        });
    }

    public function down(): void
    {
    }
};

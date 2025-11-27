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
        Schema::create('hounds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->default('');
            $table->string('url')->unique();
            $table->timestamp('last_ping')->nullable();
            $table->boolean('online')->default(false);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('hound_id')->nullable()->before('created_at');
        });
        Schema::table('prices', function (Blueprint $table) {
            $table->unsignedBigInteger('hound_id')->nullable()->before('created_at');
            $table->foreign('hound_id')->references('id')->on('hounds')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hounds');
    }
};

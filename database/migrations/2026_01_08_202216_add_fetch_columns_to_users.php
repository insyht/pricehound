<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('next_fetch')->nullable(false)->default(Carbon::create('1988-06-25 06:25:00'));
            $table->unsignedInteger('fetch_interval')->default(0)->comment('In minutes');

            $table->index('next_fetch');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};

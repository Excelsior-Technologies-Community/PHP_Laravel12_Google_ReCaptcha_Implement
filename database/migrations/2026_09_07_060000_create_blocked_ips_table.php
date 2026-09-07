<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blocked_ips', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address')->unique();
            $table->dateTime('banned_until')->nullable();
            $table->integer('failed_attempts')->default(0);
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->index('ip_address');
            $table->index('banned_until');
            $table->index('failed_attempts');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocked_ips');
    }
};

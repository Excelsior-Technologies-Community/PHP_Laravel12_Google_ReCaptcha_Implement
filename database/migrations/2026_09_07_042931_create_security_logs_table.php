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
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();

            $table->ipAddress('ip_address')->nullable();
            $table->string('email')->nullable();

            $table->string('event_type');
            $table->text('description')->nullable();

            $table->text('user_agent')->nullable();

            $table->timestamps();

            $table->index('ip_address');
            $table->index('event_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_logs');
    }
};
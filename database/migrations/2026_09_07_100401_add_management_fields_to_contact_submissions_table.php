<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_submissions', function (Blueprint $table) {

            $table->string('status')
                ->default('new')
                ->after('recaptcha_verified');

            $table->string('priority')
                ->default('medium')
                ->after('status');

            $table->text('admin_note')
                ->nullable()
                ->after('priority');

            $table->boolean('is_read')
                ->default(false)
                ->after('admin_note');
        });
    }

    public function down(): void
    {
        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'priority',
                'admin_note',
                'is_read',
            ]);
        });
    }
};

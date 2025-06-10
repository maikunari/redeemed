<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('support_email')->nullable();
            $table->string('contact_subtitle')->nullable();
            $table->string('contact_thankyou')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['support_email', 'contact_subtitle', 'contact_thankyou']);
        });
    }
}; 
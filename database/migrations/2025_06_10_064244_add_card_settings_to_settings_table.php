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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('card_website_url')->nullable();
            $table->string('card_brand_name')->nullable();
            $table->text('card_instructions')->nullable();
            $table->string('card_qr_instruction')->default('Scan to Download');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['card_website_url', 'card_brand_name', 'card_instructions', 'card_qr_instruction']);
        });
    }
};

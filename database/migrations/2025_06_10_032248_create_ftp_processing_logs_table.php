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
        Schema::create('ftp_processing_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamp('processed_at');
            $table->integer('total_files')->default(0);
            $table->integer('files_processed')->default(0);
            $table->integer('files_invalid')->default(0);
            $table->integer('files_conflicts')->default(0);
            $table->integer('files_failed')->default(0);
            $table->json('processing_details')->nullable(); // File-by-file details
            $table->json('errors')->nullable(); // Array of error messages
            $table->boolean('success')->default(true);
            $table->integer('processing_time_ms')->nullable(); // Processing duration
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['processed_at', 'success']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ftp_processing_logs');
    }
};

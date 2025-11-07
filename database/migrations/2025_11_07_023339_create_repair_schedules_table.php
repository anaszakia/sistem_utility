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
        Schema::create('repair_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_request_id')->constrained('repair_requests')->onDelete('cascade');
            $table->dateTime('scheduled_start'); // Jadwal mulai perbaikan
            $table->dateTime('scheduled_end'); // Jadwal selesai perbaikan
            $table->dateTime('actual_start')->nullable(); // Waktu aktual mulai
            $table->dateTime('actual_end')->nullable(); // Waktu aktual selesai
            $table->text('description')->nullable(); // Deskripsi jadwal
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Admin utility yang membuat jadwal
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_schedules');
    }
};

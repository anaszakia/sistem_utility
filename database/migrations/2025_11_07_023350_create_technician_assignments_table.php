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
        Schema::create('technician_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_request_id')->constrained('repair_requests')->onDelete('cascade');
            $table->foreignId('technician_id')->constrained('users')->onDelete('cascade'); // User dengan role teknisi_utility
            $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade'); // Admin utility yang menugaskan
            $table->enum('status', ['assigned', 'in_progress', 'completed'])->default('assigned');
            $table->text('notes')->nullable(); // Catatan untuk teknisi
            $table->text('work_notes')->nullable(); // Catatan pekerjaan dari teknisi
            $table->timestamp('started_at')->nullable(); // Waktu teknisi mulai kerja
            $table->timestamp('completed_at')->nullable(); // Waktu teknisi selesai kerja
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technician_assignments');
    }
};

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
        Schema::create('repair_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique(); // Nomor laporan unik
            $table->foreignId('reported_by')->constrained('users')->onDelete('cascade'); // Admin departemen yang melaporkan
            $table->string('department'); // Departemen pelapor
            $table->string('location'); // Lokasi kerusakan
            $table->string('facility_type'); // Jenis sarana/prasarana (AC, Listrik, Mesin, dll)
            $table->string('facility_name'); // Nama sarana/prasarana
            $table->text('description'); // Deskripsi kerusakan
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium'); // Prioritas
            $table->enum('status', ['pending', 'approved', 'in_progress', 'completed', 'rejected'])->default('pending'); // Status
            $table->json('images')->nullable(); // Foto kerusakan (multiple)
            $table->text('notes')->nullable(); // Catatan tambahan
            
            // Approval fields
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null'); // Admin utility yang approve
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable(); // Catatan approval
            
            // Completion fields
            $table->timestamp('completed_at')->nullable();
            $table->text('completion_notes')->nullable(); // Catatan selesai perbaikan
            $table->integer('rating')->nullable(); // Rating kepuasan (1-5)
            $table->text('feedback')->nullable(); // Feedback dari pelapor
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_requests');
    }
};

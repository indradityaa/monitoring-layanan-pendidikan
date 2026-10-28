<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            // Satu kegiatan maksimal memiliki satu laporan
            $table->foreignId('activity_id')
                ->unique()
                ->constrained('activities')
                ->cascadeOnDelete();

            // Status laporan
            $table->enum('status', [
                'draft',
                'submitted',
                'revision_needed',
                'validated',
            ])->default('draft');

            // Isi laporan
            $table->text('content')->nullable();

            // Pengirim laporan
            $table->foreignId('submitted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('submitted_at')
                ->nullable();

            // Validator laporan
            $table->foreignId('validated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('validated_at')
                ->nullable();

            // Catatan ketika laporan perlu direvisi
            $table->text('revision_note')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
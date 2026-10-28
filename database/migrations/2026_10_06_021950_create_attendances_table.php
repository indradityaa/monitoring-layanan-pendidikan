<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('activity_participant_id')
                ->unique()
                ->constrained('activity_participants')
                ->cascadeOnDelete();

            $table->enum('status', [
                'present',
                'permission',
                'absent',
            ]);

            $table->dateTime('attendance_time')->nullable();

            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
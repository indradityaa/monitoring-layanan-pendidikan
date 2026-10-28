<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();

            $table->string('code', 30)->unique();

            $table->string('name', 200);

            $table->foreignId('activity_type_id')
                ->constrained('activity_types')
                ->restrictOnDelete();

            $table->text('description')->nullable();

            $table->unsignedInteger('target_participants')->nullable();

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('person_in_charge_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('status', [
                'planned',
                'scheduled',
                'ongoing',
                'completed',
                'cancelled',
            ])->default('planned');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
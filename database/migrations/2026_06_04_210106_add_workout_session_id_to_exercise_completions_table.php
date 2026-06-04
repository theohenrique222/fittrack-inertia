<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exercise_completions', function (Blueprint $table) {
            if (! Schema::hasColumn('exercise_completions', 'workout_session_id')) {
                $table->foreignId('workout_session_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('workout_sessions')
                    ->cascadeOnDelete();
            }
        });

        Schema::table('exercise_completions', function (Blueprint $table) {
            $table->dropForeign(['workout_id']);
            $table->dropForeign(['exercise_id']);
            $table->dropForeign(['user_id']);

            try {
                $table->dropUnique(['workout_id', 'exercise_id', 'user_id']);
            } catch (Exception $e) {
                // Index may not exist
            }

            $table->foreign('workout_id')->references('id')->on('workouts')->cascadeOnDelete();
            $table->foreign('exercise_id')->references('id')->on('exercises')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['workout_id', 'exercise_id', 'user_id', 'workout_session_id'], 'exercise_completions_unique_with_session');
        });
    }

    public function down(): void
    {
        Schema::table('exercise_completions', function (Blueprint $table) {
            $table->dropUnique('exercise_completions_unique_with_session');
        });
    }
};

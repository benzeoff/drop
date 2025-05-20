<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizQuestionsTable extends Migration
{
    public function up()
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->json('options'); // Array of options (e.g., ["Option 1", "Option 2", "Option 3", "Option 4"])
            $table->integer('correct_option'); // Index of the correct option (0-based)
            $table->timestamps();
        });

        // Seed some sample questions
        \App\Models\QuizQuestion::create([
            'question' => 'Кто выиграл The International 2019 по Dota 2?',
            'options' => json_encode(['Team Liquid', 'OG', 'PSG.LGD', 'Evil Geniuses']),
            'correct_option' => 1,
        ]);
        \App\Models\QuizQuestion::create([
            'question' => 'Какой чемпион считается самым популярным в League of Legends?',
            'options' => json_encode(['Yasuo', 'Ahri', 'Lee Sin', 'Lux']),
            'correct_option' => 0,
        ]);
        // Add more questions as needed (at least 10 for variety)
    }

    public function down()
    {
        Schema::dropIfExists('quiz_questions');
    }
}

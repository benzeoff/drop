<?php

namespace App\Http\Controllers;

use App\Models\QuizQuestion;
use App\Models\QuizAttempt;
use App\Models\UserPoint;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Check if the user has already attempted the quiz today
        $lastAttempt = $user->quizAttempts()
            ->whereDate('attempted_at', Carbon::today())
            ->first();

        $canPlay = !$lastAttempt;
        $nextAttemptTime = null;
        $questions = []; // Default to empty array

        if (!$canPlay) {
            $nextAttemptTime = Carbon::today()->addDay()->startOfDay()->toDateTimeString();
        } else {
            // Fetch 10 random questions
            $questions = QuizQuestion::inRandomOrder()->take(10)->get()->map(function ($question) {
                // Парсим поле options из JSON-строки в массив
                $options = json_decode($question->options, true);
                // Если options уже является массивом, используем его напрямую, иначе парсим как JSON
                if (!is_array($options)) {
                    $options = json_decode($options, true) ?? [];
                }

                return [
                    'id' => $question->id,
                    'question' => $question->question,
                    'options' => $options,
                ];
            })->toArray(); // Ensure it’s an array
        }

        return Inertia::render('Games', [
            'canPlay' => $canPlay,
            'nextAttemptTime' => $nextAttemptTime,
            'questions' => $questions, // Always pass questions, even if empty
        ]);
    }

    public function submit(Request $request)
    {
        $user = auth()->user();

        // Validate that the user can play
        $lastAttempt = $user->quizAttempts()
            ->whereDate('attempted_at', Carbon::today())
            ->first();

        if ($lastAttempt) {
            return redirect()->back()->with('error', 'Вы уже прошли викторину сегодня. Попробуйте снова завтра!');
        }

        $answers = $request->validate([
            'answers' => 'required|array|size:10',
            'answers.*.question_id' => 'required|exists:quiz_questions,id',
            'answers.*.selected_option' => 'required|integer',
        ])['answers'];

        $correctAnswers = 0;

        foreach ($answers as $answer) {
            $question = QuizQuestion::find($answer['question_id']);
            if ($question->correct_option === $answer['selected_option']) {
                $correctAnswers++;
            } else {
                // Wrong answer, game over
                QuizAttempt::create([
                    'user_id' => $user->id,
                    'attempted_at' => now(),
                    'completed' => false,
                    'points_earned' => 0,
                ]);
                return redirect()->back()->with('error', 'Неправильный ответ! Игра окончена. Попробуйте снова завтра.');
            }
        }

        // All answers correct
        $pointsEarned = 10;
        QuizAttempt::create([
            'user_id' => $user->id,
            'attempted_at' => now(),
            'completed' => true,
            'points_earned' => $pointsEarned,
        ]);

        // Update user points
        $userPoints = $user->points ?: UserPoint::create(['user_id' => $user->id, 'points' => 0]);
        $userPoints->increment('points', $pointsEarned);

        return redirect()->route('games')->with('success', 'Поздравляем! Вы ответили правильно на все вопросы и заработали 10 баллов!');
    }
}

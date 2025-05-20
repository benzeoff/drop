<?php

namespace Database\Seeders;

use App\Models\QuizQuestion;
use Illuminate\Database\Seeder;

class QuizQuestionSeeder extends Seeder
{
    public function run()
    {
        // CS2 Questions
        QuizQuestion::create([
            'question' => 'Какой раунд начинается первым в матче?',
            'options' => json_encode(['Buy round', 'Pistol round', 'Eco round', 'Force buy']),
            'correct_option' => 1,
        ]);

        QuizQuestion::create([
            'question' => 'Какое оружие используют чаще всего у снайперов?',
            'options' => json_encode(['AWP', 'AUG', 'FAMAS', 'UMP-45']),
            'correct_option' => 0,
        ]);

        QuizQuestion::create([
            'question' => 'Сколько стоит AK-47?',
            'options' => json_encode(['$3100', '$2700', '$2900', '$2500']),
            'correct_option' => 0,
        ]);

        QuizQuestion::create([
            'question' => 'Что даёт флеш-граната?',
            'options' => json_encode(['Урон', 'Ослепление', 'Остановка', 'Восстановление HP']),
            'correct_option' => 1,
        ]);

        QuizQuestion::create([
            'question' => 'Где проводится турнир BLAST Premier?',
            'options' => json_encode(['США', 'Европа', 'Азия', 'Россия']),
            'correct_option' => 1,
        ]);

        QuizQuestion::create([
            'question' => 'Какой нож доступен в CS2?',
            'options' => json_encode(['Katana', 'Karambit', 'Dagger', 'Shuriken']),
            'correct_option' => 1,
        ]);

        QuizQuestion::create([
            'question' => 'Сколько максимум раундов в обычной соревновательной игре?',
            'options' => json_encode(['30', '24', '20', '40']),
            'correct_option' => 0,
        ]);

        QuizQuestion::create([
            'question' => 'Какой режим не существует?',
            'options' => json_encode(['Arms Race', 'Gun Game', 'Battle Royale', 'Free Roam']),
            'correct_option' => 3,
        ]);

        QuizQuestion::create([
            'question' => 'Кто из этих команд выигрывал мейджор?',
            'options' => json_encode(['Astralis', 'OpTic', 'BIG', 'OG']),
            'correct_option' => 0,
        ]);

        QuizQuestion::create([
            'question' => 'На какой карте есть место "Long A"?',
            'options' => json_encode(['Inferno', 'Nuke', 'Dust II', 'Mirage']),
            'correct_option' => 2,
        ]);

        // Dota 2 Questions
        QuizQuestion::create([
            'question' => 'Кто создал Dota?',
            'options' => json_encode(['Gabe Newell', 'IceFrog', 'Notail', 'Valve']),
            'correct_option' => 1,
        ]);

        QuizQuestion::create([
            'question' => 'Что из этого не является типом атаки?',
            'options' => json_encode(['Melee', 'Ranged', 'Aerial', 'Both']),
            'correct_option' => 2,
        ]);

        QuizQuestion::create([
            'question' => 'Сколько героев в Dota 2 (2024)?',
            'options' => json_encode(['123', '124', '125', '120']),
            'correct_option' => 1,
        ]);

        QuizQuestion::create([
            'question' => 'Какой предмет даёт невидимость?',
            'options' => json_encode(['Black King Bar', 'Blink Dagger', 'Shadow Blade', 'Manta Style']),
            'correct_option' => 2,
        ]);

        QuizQuestion::create([
            'question' => 'Какая карта используется в соревновательной игре?',
            'options' => json_encode(['Radiant vs Dire', 'Desert', 'Frosthaven', 'Arena']),
            'correct_option' => 0,
        ]);

        QuizQuestion::create([
            'question' => 'Кто выиграл The International 2018?',
            'options' => json_encode(['OG', 'Liquid', 'Secret', 'EG']),
            'correct_option' => 0,
        ]);

        QuizQuestion::create([
            'question' => 'Что даёт Aegis of the Immortal?',
            'options' => json_encode(['Урон', 'ХП', 'Воскрешение', 'Инвиз']),
            'correct_option' => 2,
        ]);

        QuizQuestion::create([
            'question' => 'Кто из героев — саппорт?',
            'options' => json_encode(['Crystal Maiden', 'Anti-Mage', 'Juggernaut', 'Phantom Assassin']),
            'correct_option' => 0,
        ]);

        QuizQuestion::create([
            'question' => 'Сколько игроков в команде?',
            'options' => json_encode(['4', '5', '6', '3']),
            'correct_option' => 1,
        ]);

        QuizQuestion::create([
            'question' => 'Какой тип урона НЕ существует?',
            'options' => json_encode(['Physical', 'Magical', 'Arcane', 'Pure']),
            'correct_option' => 2,
        ]);
    }
}

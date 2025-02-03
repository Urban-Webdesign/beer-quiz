<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Naplnění tabulky `events`
        DB::table('events')->insert([
            ['name' => 'Pioneer Beer Quiz (4. kolo 2022)', 'date' => '2022-12-08'],
            ['name' => 'Pioneer Beer Quiz (5. kolo 2022)', 'date' => '2022-12-22'],
            ['name' => 'Pioneer Beer Quiz (1. kolo 2023)', 'date' => '2023-01-19'],
            ['name' => 'Pioneer Beer Quiz (3. kolo 2023)', 'date' => '2023-03-23'],
            ['name' => 'Pioneer Beer Quiz (5. kolo 2023)', 'date' => '2023-10-19'],
            ['name' => 'Pioneer Beer Quiz (6. kolo 2023)', 'date' => '2023-11-09'],
            ['name' => 'Pioneer Beer Quiz (7. kolo 2023)', 'date' => '2023-12-14'],
            ['name' => 'Pioneer Beer Quiz (1. kolo 2024)', 'date' => '2024-01-18'],
            ['name' => 'Pioneer Beer Quiz (2. kolo 2024)', 'date' => '2024-02-22'],
            ['name' => 'Pioneer Beer Quiz (3. kolo 2024)', 'date' => '2024-03-21'],
            ['name' => 'Pioneer Beer Quiz (4. kolo 2024)', 'date' => '2024-04-25'],
            ['name' => 'Pioneer Beer Quiz (6. kolo 2024)', 'date' => '2024-11-21'],
            ['name' => 'Pioneer Beer Quiz (7. kolo 2024)', 'date' => '2024-12-19'],
            ['name' => 'Pioneer Beer Quiz (1. kolo 2025)', 'date' => '2025-01-23'],
        ]);

        // Naplnění tabulky `teams`
        DB::table('teams')->insert([
            ['name' => 'Opera'], // 1
            ['name' => 'Mrokaři'], // 2
            ['name' => 'Ptactvo'], // 3
            ['name' => 'Perspektivní zlo'], // 4
            ['name' => 'Tři hlavy'], // 5
            ['name' => 'Starý páky'], // 6
            ['name' => 'ACS'], // 7
            ['name' => 'Kočičky'], // 8
            ['name' => 'Hopmani'], // 9
            ['name' => 'Činohra'], // 10
            ['name' => 'Kopretiny'], // 11
            ['name' => 'Pusinky'], // 12
            ['name' => 'Sosáci'], // 13
            ['name' => 'MMK'], // 14
            ['name' => 'V h..?'], // 15
            ['name' => 'Blondýny'], // 16
            ['name' => 'ŽOK'], // 17
            ['name' => 'Pivolky'], // 18
            ['name' => 'Lupulíni'], // 19
            ['name' => 'Triple idiot'], // 20
            ['name' => '?'], // 21
            ['name' => 'Ing. a Radek'], // 22
            ['name' => 'Quadr I'], // 23
            ['name' => 'Kromaňonci'], // 23
        ]);

        // Naplnění tabulky `results`
        DB::table('results')->insert([

            // 1
            ['event_id' => 1, 'team_id' => 10, 'score' => 9, 'position' => 4],
            ['event_id' => 1, 'team_id' => 24, 'score' => 8, 'position' => 6],
            ['event_id' => 1, 'team_id' => 3, 'score' => 6, 'position' => 7],
            ['event_id' => 1, 'team_id' => 21, 'score' => 9, 'position' => 4],
            ['event_id' => 1, 'team_id' => 20, 'score' => 12, 'position' => 2],
            ['event_id' => 1, 'team_id' => 2, 'score' => 11, 'position' => 3],
            ['event_id' => 1, 'team_id' => 1, 'score' => 14, 'position' => 1],
            // 2
            ['event_id' => 2, 'team_id' => 19, 'score' => 10, 'position' => 3],
            ['event_id' => 2, 'team_id' => 4, 'score' => 7, 'position' => 6],
            ['event_id' => 2, 'team_id' => 2, 'score' => 12, 'position' => 1],
            ['event_id' => 2, 'team_id' => 3, 'score' => 9, 'position' => 4],
            ['event_id' => 2, 'team_id' => 21, 'score' => 5, 'position' => 7],
            ['event_id' => 2, 'team_id' => 20, 'score' => 9, 'position' => 4],
            ['event_id' => 2, 'team_id' => 23, 'score' => 5, 'position' => 7],
            ['event_id' => 2, 'team_id' => 1, 'score' => 12, 'position' => 2],
            // 3
            ['event_id' => 3, 'team_id' => 1, 'score' => 13, 'position' => 5],
            ['event_id' => 3, 'team_id' => 4, 'score' => 16, 'position' => 1],
            ['event_id' => 3, 'team_id' => 3, 'score' => 13, 'position' => 5],
            ['event_id' => 3, 'team_id' => 2, 'score' => 15, 'position' => 2],
            ['event_id' => 3, 'team_id' => 7, 'score' => 14, 'position' => 3],
            ['event_id' => 3, 'team_id' => 9, 'score' => 11, 'position' => 8],
            ['event_id' => 3, 'team_id' => 20, 'score' => 12, 'position' => 7],
            ['event_id' => 3, 'team_id' => 17, 'score' => 14, 'position' => 3],
            // 4
            ['event_id' => 4, 'team_id' => 10, 'score' => 13, 'position' => 1],
            ['event_id' => 4, 'team_id' => 1, 'score' => 11, 'position' => 2],
            ['event_id' => 4, 'team_id' => 3, 'score' => 10, 'position' => 4],
            ['event_id' => 4, 'team_id' => 21, 'score' => 6, 'position' => 6],
            ['event_id' => 4, 'team_id' => 7, 'score' => 10, 'position' => 4],
            ['event_id' => 4, 'team_id' => 9, 'score' => 5, 'position' => 8],
            ['event_id' => 4, 'team_id' => 17, 'score' => 11, 'position' => 2],
            ['event_id' => 4, 'team_id' => 22, 'score' => 6, 'position' => 6],
            // 5
            ['event_id' => 5, 'team_id' => 1, 'score' => 14, 'position' => 2],
            ['event_id' => 5, 'team_id' => 4, 'score' => 8, 'position' => 8],
            ['event_id' => 5, 'team_id' => 3, 'score' => 11, 'position' => 6],
            ['event_id' => 5, 'team_id' => 19, 'score' => 18, 'position' => 1],
            ['event_id' => 5, 'team_id' => 7, 'score' => 12, 'position' => 4],
            ['event_id' => 5, 'team_id' => 9, 'score' => 13, 'position' => 3],
            ['event_id' => 5, 'team_id' => 20, 'score' => 12, 'position' => 4],
            ['event_id' => 5, 'team_id' => 10, 'score' => 9, 'position' => 7],
            // 6
            ['event_id' => 6, 'team_id' => 1, 'score' => 12, 'position' => 3],
            ['event_id' => 6, 'team_id' => 3, 'score' => 9, 'position' => 4],
            ['event_id' => 6, 'team_id' => 4, 'score' => 9, 'position' => 4],
            ['event_id' => 6, 'team_id' => 9, 'score' => 14, 'position' => 2],
            ['event_id' => 6, 'team_id' => 10, 'score' => 15, 'position' => 1],
            // 7
            ['event_id' => 7, 'team_id' => 1, 'score' => 16, 'position' => 1],
            ['event_id' => 7, 'team_id' => 3, 'score' => 12, 'position' => 5],
            ['event_id' => 7, 'team_id' => 6, 'score' => 9, 'position' => 8],
            ['event_id' => 7, 'team_id' => 7, 'score' => 14, 'position' => 3],
            ['event_id' => 7, 'team_id' => 18, 'score' => 12, 'position' => 5],
            ['event_id' => 7, 'team_id' => 2, 'score' => 13, 'position' => 4],
            ['event_id' => 7, 'team_id' => 9, 'score' => 11, 'position' => 7],
            ['event_id' => 7, 'team_id' => 19, 'score' => 15, 'position' => 2],
            // 8
            ['event_id' => 8, 'team_id' => 1, 'score' => 15, 'position' => 1],
            ['event_id' => 8, 'team_id' => 3, 'score' => 6, 'position' => 7],
            ['event_id' => 8, 'team_id' => 10, 'score' => 10, 'position' => 3],
            ['event_id' => 8, 'team_id' => 6, 'score' => 5, 'position' => 8],
            ['event_id' => 8, 'team_id' => 7, 'score' => 11, 'position' => 2],
            ['event_id' => 8, 'team_id' => 18, 'score' => 8, 'position' => 6],
            ['event_id' => 8, 'team_id' => 2, 'score' => 10, 'position' => 3],
            ['event_id' => 8, 'team_id' => 5, 'score' => 10, 'position' => 3],
            // 9
            ['event_id' => 9, 'team_id' => 1, 'score' => 13, 'position' => 1],
            ['event_id' => 9, 'team_id' => 4, 'score' => 9, 'position' => 7],
            ['event_id' => 9, 'team_id' => 6, 'score' => 4, 'position' => 8],
            ['event_id' => 9, 'team_id' => 2, 'score' => 13, 'position' => 2],
            ['event_id' => 9, 'team_id' => 3, 'score' => 9, 'position' => 6],
            ['event_id' => 9, 'team_id' => 9, 'score' => 11, 'position' => 4],
            ['event_id' => 9, 'team_id' => 17, 'score' => 12, 'position' => 3],
            ['event_id' => 9, 'team_id' => 5, 'score' => 10, 'position' => 5],
            // 10
            ['event_id' => 10, 'team_id' => 1, 'score' => 9, 'position' => 3],
            ['event_id' => 10, 'team_id' => 10, 'score' => 11, 'position' => 2],
            ['event_id' => 10, 'team_id' => 9, 'score' => 9, 'position' => 3],
            ['event_id' => 10, 'team_id' => 7, 'score' => 9, 'position' => 3],
            ['event_id' => 10, 'team_id' => 2, 'score' => 8, 'position' => 6],
            ['event_id' => 10, 'team_id' => 16, 'score' => 7, 'position' => 7],
            ['event_id' => 10, 'team_id' => 5, 'score' => 7, 'position' => 7],
            ['event_id' => 10, 'team_id' => 3, 'score' => 14, 'position' => 1],
            // 11
            ['event_id' => 11, 'team_id' => 1, 'score' => 14, 'position' => 1],
            ['event_id' => 11, 'team_id' => 3, 'score' => 13, 'position' => 2],
            ['event_id' => 11, 'team_id' => 6, 'score' => 13, 'position' => 2],
            ['event_id' => 11, 'team_id' => 7, 'score' => 12, 'position' => 5],
            ['event_id' => 11, 'team_id' => 8, 'score' => 9, 'position' => 8],
            ['event_id' => 11, 'team_id' => 9, 'score' => 12, 'position' => 5],
            ['event_id' => 11, 'team_id' => 10, 'score' => 10, 'position' => 7],
            ['event_id' => 11, 'team_id' => 12, 'score' => 13, 'position' => 2],
            // 12
            ['event_id' => 12, 'team_id' => 1, 'score' => 12, 'position' => 3],
            ['event_id' => 12, 'team_id' => 3, 'score' => 13, 'position' => 1],
            ['event_id' => 12, 'team_id' => 6, 'score' => 10, 'position' => 7],
            ['event_id' => 12, 'team_id' => 7, 'score' => 12, 'position' => 3],
            ['event_id' => 12, 'team_id' => 10, 'score' => 11, 'position' => 5],
            ['event_id' => 12, 'team_id' => 9, 'score' => 13, 'position' => 2],
            ['event_id' => 12, 'team_id' => 5, 'score' => 11, 'position' => 5],
            ['event_id' => 12, 'team_id' => 15, 'score' => 9, 'position' => 8],
            // 13
            ['event_id' => 13, 'team_id' => 1, 'score' => 14, 'position' => 2],
            ['event_id' => 13, 'team_id' => 11, 'score' => 9, 'position' => 7],
            ['event_id' => 13, 'team_id' => 3, 'score' => 15, 'position' => 1],
            ['event_id' => 13, 'team_id' => 6, 'score' => 11, 'position' => 6],
            ['event_id' => 13, 'team_id' => 7, 'score' => 13, 'position' => 5],
            ['event_id' => 13, 'team_id' => 9, 'score' => 14, 'position' => 2],
            ['event_id' => 13, 'team_id' => 5, 'score' => 14, 'position' => 2],
            ['event_id' => 13, 'team_id' => 10, 'score' => 8, 'position' => 8],
            // 14
            ['event_id' => 14, 'team_id' => 1, 'score' => 8, 'position' => 6],
            ['event_id' => 14, 'team_id' => 2, 'score' => 13, 'position' => 1],
            ['event_id' => 14, 'team_id' => 3, 'score' => 9, 'position' => 3],
            ['event_id' => 14, 'team_id' => 6, 'score' => 9, 'position' => 3],
            ['event_id' => 14, 'team_id' => 7, 'score' => 12, 'position' => 2],
            ['event_id' => 14, 'team_id' => 9, 'score' => 8, 'position' => 6],
            ['event_id' => 14, 'team_id' => 11, 'score' => 9, 'position' => 3],
            ['event_id' => 14, 'team_id' => 13, 'score' => 8, 'position' => 6],
            ['event_id' => 14, 'team_id' => 14, 'score' => 8, 'position' => 6],
        ]);
    }
}

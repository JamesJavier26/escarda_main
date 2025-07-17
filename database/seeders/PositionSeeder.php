<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'title' => 'Web Developer',        'code' => 'WEBDEV', 'is_managerial' => true ],
            ['id' => 2, 'title' => 'Sales Manager',        'code' => 'SM',     'is_managerial' => true ],
            ['id' => 3, 'title' => 'Operations Manager',   'code' => 'OM',     'is_managerial' => true ],
            ['id' => 4, 'title' => 'Sales Staff',          'code' => 'SS',     'is_managerial' => false],
            ['id' => 5, 'title' => 'Operation Staff',      'code' => 'OS',     'is_managerial' => false],
            ['id' => 6, 'title' => 'Production Staff',     'code' => 'PS',     'is_managerial' => false],
        ];

        foreach ($rows as $row) {
            Position::updateOrCreate(
                ['id' => $row['id']],
                array_merge($row, [
                    'salary_min'  => null,
                    'salary_max'  => null,
                    'is_active'   => true,
                    'created_at'  => Carbon::parse('2025-06-16 00:00:00')->addSeconds($row['id']),
                    'updated_at'  => null,
                    'deleted_at'  => null,
                ])
            );
        }
    }
}

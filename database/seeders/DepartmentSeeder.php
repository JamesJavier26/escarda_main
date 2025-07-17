<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            ['id' => 1, 'name' => 'IT',         'code' => 'IT',   'description' => null],
            ['id' => 2, 'name' => 'Sales',      'code' => 'SL',   'description' => null],
            ['id' => 3, 'name' => 'Operations', 'code' => 'OP',   'description' => null],
            ['id' => 4, 'name' => 'Production', 'code' => 'PROD', 'description' => null],
        ];

        foreach ($rows as $row) {
            Department::updateOrCreate(
                ['id' => $row['id']],
                array_merge($row, [
                    'is_active'   => true,
                    'created_at'  => Carbon::parse('2025-06-16 00:00:00')->addSeconds($row['id']),
                    'updated_at'  => null,
                    'deleted_at'  => null,
                ])
            );
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('customer_categories')->insert([
            ['name' => 'Retaile'],
            ['name' => 'Wholesale'],
            ['name' => 'VIP'],
            ['name' => 'Government'],
        ]); 
    }
}

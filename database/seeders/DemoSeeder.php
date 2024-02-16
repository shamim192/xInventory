<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\Category;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Unit seeder
        Unit::insert([
            [
                'base_unit_id' => 1,
                'name' => '25KG',
                'quantity' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'base_unit_id' => 4,
                'name' => '5Litre',
                'quantity' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Category seeder
        Category::insert([
            [
                'name' => 'Electronics',
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Clothing',
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        Category::insert([            
                [
                    'name' => 'Laptops',
                    'parent_id' => 1,
                    'status' => 'Active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Smartphones',
                    'parent_id' => 1,
                    'status' => 'Active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Men\'s Clothing',
                    'parent_id' => 2,
                    'status' => 'Active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Women\'s Clothing',
                    'parent_id' => 2,
                    'status' => 'Active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
        ]);       
        
    }
}

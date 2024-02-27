<?php

namespace Database\Seeders;

use App\Models\BaseUnit;
use Illuminate\Database\Seeder;

class BaseUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BaseUnit::create([
            'name' => 'KG',            
        ]);
        BaseUnit::create([
            'name' => 'gm',            
        ]);
        BaseUnit::create([
            'name' => 'Goj',            
        ]);
        BaseUnit::create([
            'name' => 'Litre',            
        ]);
        BaseUnit::create([
            'name' => 'PCs',            
        ]);
        BaseUnit::create([
            'name' => 'm',            
        ]);
        BaseUnit::create([
            'name' => 'mm',            
        ]);
        BaseUnit::create([
            'name' => 'Feet',            
        ]);
        BaseUnit::create([
            'name' => 'SFT',            
        ]);
        BaseUnit::create([
            'name' => 'ml',            
        ]);
    }
}

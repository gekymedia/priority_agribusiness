<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Poultry Categories
            ['name' => 'Feed', 'type' => 'poultry', 'description' => 'Poultry feed expenses'],
            ['name' => 'Medication', 'type' => 'poultry', 'description' => 'Vaccines and medications'],
            ['name' => 'Labor', 'type' => 'poultry', 'description' => 'Labor costs for poultry operations'],
            ['name' => 'Utilities', 'type' => 'poultry', 'description' => 'Water, electricity for poultry houses'],
            ['name' => 'Equipment', 'type' => 'poultry', 'description' => 'Poultry equipment and maintenance'],
            
            // Crop Categories
            ['name' => 'Seeds', 'type' => 'crop', 'description' => 'Seed purchases'],
            ['name' => 'Fertilizer', 'type' => 'crop', 'description' => 'Fertilizer and soil amendments'],
            ['name' => 'Pesticides', 'type' => 'crop', 'description' => 'Pest control products'],
            ['name' => 'Irrigation', 'type' => 'crop', 'description' => 'Water and irrigation costs'],
            ['name' => 'Harvesting', 'type' => 'crop', 'description' => 'Harvesting labor and equipment'],
            
            // General Categories
            ['name' => 'Transportation', 'type' => 'general', 'description' => 'Transport and logistics'],
            ['name' => 'Administration', 'type' => 'general', 'description' => 'Administrative expenses'],
            ['name' => 'Maintenance', 'type' => 'general', 'description' => 'General maintenance costs'],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::create($category);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Farm;
use App\Models\House;
use App\Models\Field;
use App\Models\BirdBatch;
use App\Models\BirdBatchRecord;
use App\Models\MedicationRecord;
use App\Models\EggProduction;
use App\Models\Planting;
use App\Models\CropActivity;
use App\Models\Harvest;
use App\Models\Task;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AgribusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Users
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@agribusiness.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $manager = User::create([
            'name' => 'Farm Manager',
            'email' => 'manager@agribusiness.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
        ]);

        $farmer = User::create([
            'name' => 'John Farmer',
            'email' => 'farmer@agribusiness.com',
            'password' => Hash::make('password'),
            'role' => 'farmer',
        ]);

        // Create Farms
        $poultryFarm = Farm::create([
            'name' => 'Green Valley Poultry Farm',
            'location' => 'Kumasi, Ashanti Region',
            'description' => 'A modern poultry farm specializing in layer and broiler production',
            'farm_type' => 'poultry',
        ]);

        $cropFarm = Farm::create([
            'name' => 'Sunshine Crop Farm',
            'location' => 'Tamale, Northern Region',
            'description' => 'Large-scale crop production farm with multiple fields',
            'farm_type' => 'crop',
        ]);

        $mixedFarm = Farm::create([
            'name' => 'Harmony Mixed Farm',
            'location' => 'Accra, Greater Accra Region',
            'description' => 'Integrated farm combining poultry and crop production',
            'farm_type' => 'mixed',
        ]);

        // Create Houses for Poultry Farm
        $house1 = House::create([
            'farm_id' => $poultryFarm->id,
            'name' => 'Layer House A',
            'capacity' => 5000,
            'type' => 'layer',
        ]);

        $house2 = House::create([
            'farm_id' => $poultryFarm->id,
            'name' => 'Broiler House B',
            'capacity' => 3000,
            'type' => 'broiler',
        ]);

        $house3 = House::create([
            'farm_id' => $mixedFarm->id,
            'name' => 'Layer House 1',
            'capacity' => 2000,
            'type' => 'layer',
        ]);

        // Create Fields for Crop Farm
        $field1 = Field::create([
            'farm_id' => $cropFarm->id,
            'name' => 'Maize Field North',
            'size' => 10.5,
            'soil_type' => 'loamy',
            'description' => 'Primary maize production field',
        ]);

        $field2 = Field::create([
            'farm_id' => $cropFarm->id,
            'name' => 'Tomato Field South',
            'size' => 5.0,
            'soil_type' => 'sandy loam',
            'description' => 'Tomato cultivation area',
        ]);

        $field3 = Field::create([
            'farm_id' => $mixedFarm->id,
            'name' => 'Vegetable Plot',
            'size' => 3.0,
            'soil_type' => 'clay loam',
            'description' => 'Mixed vegetable production',
        ]);

        // Create Bird Batches
        $batch1 = BirdBatch::create([
            'farm_id' => $poultryFarm->id,
            'house_id' => $house1->id,
            'batch_code' => 'LV-2024-001',
            'breed' => 'Lohmann Brown',
            'purpose' => 'egg_production',
            'arrival_date' => Carbon::now()->subMonths(3),
            'quantity_arrived' => 4500,
            'cost_per_bird' => 8.50,
            'supplier_name' => 'Premium Poultry Suppliers',
            'status' => 'active',
        ]);

        $batch2 = BirdBatch::create([
            'farm_id' => $poultryFarm->id,
            'house_id' => $house2->id,
            'batch_code' => 'BR-2024-002',
            'breed' => 'Cobb 500',
            'purpose' => 'meat_production',
            'arrival_date' => Carbon::now()->subWeeks(2),
            'quantity_arrived' => 2800,
            'cost_per_bird' => 6.00,
            'supplier_name' => 'AgriTech Broilers',
            'status' => 'active',
        ]);

        $batch3 = BirdBatch::create([
            'farm_id' => $mixedFarm->id,
            'house_id' => $house3->id,
            'batch_code' => 'LV-2024-003',
            'breed' => 'ISA Brown',
            'purpose' => 'egg_production',
            'arrival_date' => Carbon::now()->subMonths(2),
            'quantity_arrived' => 1800,
            'cost_per_bird' => 9.00,
            'supplier_name' => 'Local Breeder Co.',
            'status' => 'active',
        ]);

        // Create Bird Batch Records
        BirdBatchRecord::create([
            'bird_batch_id' => $batch1->id,
            'record_date' => Carbon::now()->subDays(1),
            'mortality_count' => 2,
            'cull_count' => 0,
            'feed_used_kg' => 450.5,
            'water_used_litres' => 1200,
            'average_weight_kg' => 1.85,
            'notes' => 'Birds in good health, normal feed consumption',
        ]);

        BirdBatchRecord::create([
            'bird_batch_id' => $batch2->id,
            'record_date' => Carbon::now()->subDays(1),
            'mortality_count' => 1,
            'cull_count' => 0,
            'feed_used_kg' => 380.0,
            'water_used_litres' => 950,
            'average_weight_kg' => 1.2,
            'notes' => 'Growing well, on track for target weight',
        ]);

        // Create Medication Records
        MedicationRecord::create([
            'bird_batch_id' => $batch1->id,
            'date' => Carbon::now()->subWeeks(2),
            'medication_name' => 'Newcastle Vaccine',
            'dosage' => '1ml per liter',
            'quantity_used' => 4500,
            'cost' => 250.00,
            'purpose' => 'vaccine',
            'notes' => 'Routine vaccination program',
        ]);

        MedicationRecord::create([
            'bird_batch_id' => $batch2->id,
            'date' => Carbon::now()->subDays(5),
            'medication_name' => 'Antibiotic Treatment',
            'dosage' => '500mg per kg feed',
            'quantity_used' => 2800,
            'cost' => 180.00,
            'purpose' => 'antibiotic',
            'notes' => 'Preventive treatment for respiratory issues',
        ]);

        // Create Egg Production Records
        EggProduction::create([
            'bird_batch_id' => $batch1->id,
            'date' => Carbon::now()->subDays(1),
            'eggs_collected' => 3850,
            'cracked_or_damaged' => 15,
            'eggs_used_internal' => 50,
            'notes' => 'Excellent production rate, 85.5% lay rate',
        ]);

        EggProduction::create([
            'bird_batch_id' => $batch3->id,
            'date' => Carbon::now()->subDays(1),
            'eggs_collected' => 1520,
            'cracked_or_damaged' => 8,
            'eggs_used_internal' => 20,
            'notes' => 'Good production, birds adapting well',
        ]);

        // Create Plantings
        $planting1 = Planting::create([
            'field_id' => $field1->id,
            'crop_name' => 'Maize',
            'planting_date' => Carbon::now()->subMonths(2),
            'expected_harvest_date' => Carbon::now()->addMonths(2),
            'seed_source' => 'Certified Seeds Ltd',
            'quantity_planted' => 250,
            'status' => 'growing',
        ]);

        $planting2 = Planting::create([
            'field_id' => $field2->id,
            'crop_name' => 'Tomato',
            'planting_date' => Carbon::now()->subWeeks(3),
            'expected_harvest_date' => Carbon::now()->addWeeks(5),
            'seed_source' => 'Horticultural Seeds Co.',
            'quantity_planted' => 5000,
            'status' => 'growing',
        ]);

        $planting3 = Planting::create([
            'field_id' => $field3->id,
            'crop_name' => 'Cabbage',
            'planting_date' => Carbon::now()->subWeeks(4),
            'expected_harvest_date' => Carbon::now()->addWeeks(2),
            'seed_source' => 'Local Supplier',
            'quantity_planted' => 3000,
            'status' => 'growing',
        ]);

        // Create Crop Activities
        CropActivity::create([
            'planting_id' => $planting1->id,
            'date' => Carbon::now()->subWeeks(4),
            'activity_type' => 'fertilization',
            'notes' => 'Applied NPK fertilizer - First top dressing applied',
        ]);

        CropActivity::create([
            'planting_id' => $planting2->id,
            'date' => Carbon::now()->subDays(2),
            'activity_type' => 'irrigation',
            'notes' => 'Drip irrigation system maintenance - System working efficiently',
        ]);

        CropActivity::create([
            'planting_id' => $planting3->id,
            'date' => Carbon::now()->subDays(5),
            'activity_type' => 'pest_control',
            'notes' => 'Organic pesticide application - Preventive treatment for aphids',
        ]);

        // Create Harvests
        Harvest::create([
            'planting_id' => $planting1->id,
            'harvest_date' => Carbon::now()->subWeeks(1),
            'quantity_harvested' => 8500,
            'notes' => 'First harvest, good yield - Quality Grade A - Stored in Warehouse A',
        ]);

        // Create Tasks
        Task::create([
            'related_type' => 'App\Models\House',
            'related_id' => $house1->id,
            'title' => 'Feed birds in Layer House A',
            'description' => 'Distribute morning feed to all birds',
            'due_date' => Carbon::now()->addDays(1),
            'priority' => 'high',
            'status' => 'pending',
            'created_by' => $manager->id,
        ]);

        Task::create([
            'related_type' => 'App\Models\Field',
            'related_id' => $field2->id,
            'title' => 'Inspect tomato field for pests',
            'description' => 'Check for signs of pest infestation',
            'due_date' => Carbon::now()->addDays(2),
            'priority' => 'medium',
            'status' => 'pending',
            'created_by' => $manager->id,
        ]);

        Task::create([
            'related_type' => 'App\Models\Farm',
            'related_id' => $poultryFarm->id,
            'title' => 'Collect eggs from all houses',
            'description' => 'Daily egg collection and sorting',
            'due_date' => Carbon::now(),
            'priority' => 'high',
            'status' => 'pending',
            'created_by' => $manager->id,
        ]);

        Task::create([
            'title' => 'Prepare monthly financial report',
            'description' => 'Compile income and expense reports',
            'due_date' => Carbon::now()->addWeeks(1),
            'priority' => 'medium',
            'status' => 'pending',
            'created_by' => $admin->id,
        ]);

        $this->command->info('Agribusiness demo data seeded successfully!');
        $this->command->info('Users created:');
        $this->command->info('  - Admin: admin@agribusiness.com / password');
        $this->command->info('  - Manager: manager@agribusiness.com / password');
        $this->command->info('  - Farmer: farmer@agribusiness.com / password');
    }
}

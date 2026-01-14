<?php

namespace Database\Seeders;

use App\Models\MedicationCalendar;
use Illuminate\Database\Seeder;

class MedicationCalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing calendars to avoid duplicates
        MedicationCalendar::query()->delete();

        // Broiler Standard Medication Calendar (35-42 days)
        MedicationCalendar::create([
            'name' => 'Broiler Standard Schedule',
            'type' => 'broiler',
            'description' => 'Commonly approved medication and vaccination schedule for broiler chickens (35-42 days). Based on standard commercial practices.',
            'is_default' => true,
            'is_active' => true,
            'schedule' => [
                [
                    'week' => 1,
                    'day' => 1,
                    'medication_name' => 'Electrolytes & Vitamin C',
                    'description' => 'Stress management and hydration support - Administer electrolytes and vitamin C to reduce stress',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Water',
                ],
                [
                    'week' => 1,
                    'day' => 1,
                    'medication_name' => 'Amino Acids & Multivitamins',
                    'description' => 'Growth and immunity booster - Promote growth and enhance immunity',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Water',
                ],
                [
                    'week' => 1,
                    'day' => 3,
                    'medication_name' => 'Respiratory Herbs (Preventive)',
                    'description' => 'Preventive respiratory health - Administer to prevent CRD and E. coli',
                    'dosage' => '1 ml per 100 birds',
                    'method' => 'Water',
                ],
                [
                    'week' => 1,
                    'day' => 5,
                    'medication_name' => 'Newcastle Disease Vaccine (Lasota)',
                    'description' => 'Newcastle Disease vaccination - Administer via eye drop or drinking water',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Water/Eye Drop',
                ],
                [
                    'week' => 1,
                    'day' => 3,
                    'medication_name' => 'Coccidiosis Prevention',
                    'description' => 'Coccidiosis prevention - Administer coccidiostat (e.g., Amprolium)',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Water',
                ],
                [
                    'week' => 2,
                    'day' => 1,
                    'medication_name' => 'Infectious Bursal Disease (IBD) Vaccine',
                    'description' => 'Gumboro Disease vaccination - Administer via drinking water',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Water',
                ],
                [
                    'week' => 2,
                    'day' => 1,
                    'medication_name' => 'Vitamin B Complex',
                    'description' => 'Energy metabolism support - Support energy metabolism and overall vitality',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Water',
                ],
                [
                    'week' => 3,
                    'day' => 1,
                    'medication_name' => 'IBD Vaccine Booster',
                    'description' => 'Infectious Bursal Disease booster - Ensure continued immunity',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Water',
                ],
                [
                    'week' => 3,
                    'day' => 1,
                    'medication_name' => 'Deworming',
                    'description' => 'Internal parasite control - Administer dewormer (e.g., Piperazine)',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Water',
                ],
                [
                    'week' => 3,
                    'day' => 1,
                    'medication_name' => 'Liver Tonic',
                    'description' => 'Liver function enhancement - Improve feed conversion ratio and digestion',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Water',
                ],
                [
                    'week' => 4,
                    'day' => 1,
                    'medication_name' => 'Newcastle Disease Booster',
                    'description' => 'Newcastle Disease booster vaccination - Reinforce immunity',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Water',
                ],
                [
                    'week' => 4,
                    'day' => 1,
                    'medication_name' => 'Calcium & Vitamin D3',
                    'description' => 'Bone development support - Essential for bone development and muscle function',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Water',
                ],
                [
                    'week' => 5,
                    'day' => 1,
                    'medication_name' => 'Vitamin E & Selenium',
                    'description' => 'Antioxidant support - Enhance antioxidant capacity and overall health',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Water',
                ],
                [
                    'week' => 5,
                    'day' => 1,
                    'medication_name' => 'Pre-Slaughter Health Check',
                    'description' => 'Final health check before processing - Ensure birds are healthy for slaughter',
                    'dosage' => 'N/A',
                    'method' => 'Check',
                ],
            ],
        ]);

        // Layer Standard Medication Calendar (0-20 weeks pullet phase)
        MedicationCalendar::create([
            'name' => 'Layer Standard Schedule',
            'type' => 'layer',
            'description' => 'Commonly approved medication and vaccination schedule for layer pullets (0-20 weeks). Standard commercial layer vaccination program.',
            'is_default' => true,
            'is_active' => true,
            'schedule' => [
                [
                    'week' => 1,
                    'day' => 1,
                    'medication_name' => 'Marek\'s Disease Vaccine',
                    'description' => 'Marek\'s Disease vaccination - Typically administered at hatchery, but can be given on arrival',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Injection (Subcutaneous)',
                ],
                [
                    'week' => 1,
                    'day' => 1,
                    'medication_name' => 'Electrolytes & Vitamins',
                    'description' => 'Stress management - Administer electrolytes and vitamins to reduce stress and support hydration',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Water',
                ],
                [
                    'week' => 1,
                    'day' => 5,
                    'medication_name' => 'Newcastle Disease Vaccine (Lasota)',
                    'description' => 'Newcastle Disease vaccination - Administer via eye drop or drinking water',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Water/Eye Drop',
                ],
                [
                    'week' => 1,
                    'day' => 3,
                    'medication_name' => 'Coccidiosis Prevention',
                    'description' => 'Coccidiosis prevention - Administer coccidiostat in feed or water',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Feed/Water',
                ],
                [
                    'week' => 2,
                    'day' => 1,
                    'medication_name' => 'Infectious Bursal Disease (IBD) Vaccine',
                    'description' => 'Gumboro Disease vaccination - Administer via drinking water',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Water',
                ],
                [
                    'week' => 2,
                    'day' => 1,
                    'medication_name' => 'Vitamin B Complex',
                    'description' => 'Energy metabolism support - Support energy metabolism and overall vitality',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Water',
                ],
                [
                    'week' => 3,
                    'day' => 1,
                    'medication_name' => 'Newcastle Disease Booster',
                    'description' => 'Newcastle Disease booster vaccination - Reinforce immunity',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Water',
                ],
                [
                    'week' => 3,
                    'day' => 1,
                    'medication_name' => 'Deworming',
                    'description' => 'Internal parasite control - Administer dewormer (e.g., Piperazine or Levamisole)',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Water',
                ],
                [
                    'week' => 4,
                    'day' => 1,
                    'medication_name' => 'IBD Vaccine Booster',
                    'description' => 'Infectious Bursal Disease booster - Ensure continued immunity',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Water',
                ],
                [
                    'week' => 6,
                    'day' => 1,
                    'medication_name' => 'Fowl Pox Vaccine',
                    'description' => 'Fowl Pox vaccination - Administer via wing web stab method',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Wing Web Stab',
                ],
                [
                    'week' => 8,
                    'day' => 1,
                    'medication_name' => 'Infectious Bronchitis Vaccine (IB)',
                    'description' => 'Infectious Bronchitis vaccination - Administer via drinking water',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Water',
                ],
                [
                    'week' => 10,
                    'day' => 1,
                    'medication_name' => 'Newcastle Disease & IB Booster',
                    'description' => 'Combined Newcastle Disease and Infectious Bronchitis booster',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Water',
                ],
                [
                    'week' => 12,
                    'day' => 1,
                    'medication_name' => 'Deworming (Second Round)',
                    'description' => 'Second deworming - Administer dewormer for continued parasite control',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Water',
                ],
                [
                    'week' => 12,
                    'day' => 1,
                    'medication_name' => 'Fowl Cholera Vaccine',
                    'description' => 'Fowl Cholera vaccination - Administer via injection',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Injection',
                ],
                [
                    'week' => 14,
                    'day' => 1,
                    'medication_name' => 'Newcastle Disease & IB Booster (Second)',
                    'description' => 'Second combined Newcastle and IB booster - Reinforce immunity before lay',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Water',
                ],
                [
                    'week' => 16,
                    'day' => 1,
                    'medication_name' => 'Pre-Lay Health Check',
                    'description' => 'Comprehensive health check before laying period - Check body weight, uniformity, and overall health',
                    'dosage' => 'N/A',
                    'method' => 'Health Check',
                ],
                [
                    'week' => 18,
                    'day' => 1,
                    'medication_name' => 'Newcastle Disease & IB Booster (Pre-Lay)',
                    'description' => 'Final pre-lay vaccination booster - Ensure strong immunity before production begins',
                    'dosage' => 'As per manufacturer instructions',
                    'method' => 'Water',
                ],
                [
                    'week' => 20,
                    'day' => 1,
                    'medication_name' => 'Production Health Check',
                    'description' => 'Production readiness check - Verify birds are ready for egg production phase',
                    'dosage' => 'N/A',
                    'method' => 'Health Check',
                ],
            ],
        ]);
    }
}

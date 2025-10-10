<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ViolationCategory;

class ViolationCategorySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'category_id' => 'VC001',
                'category_name' => 'Academic Misconduct',
                'description' => 'Cheating, plagiarism, or falsifying records.',
                'severity_level' => 'High',
                'default_sanction' => 'Suspension',
                'status' => 'Active',
            ],
            [
                'category_id' => 'VC002',
                'category_name' => 'Behavioral Misconduct',
                'description' => 'Disrespect, bullying, or harassment.',
                'severity_level' => 'Medium',
                'default_sanction' => 'Warning/Reprimand',
                'status' => 'Active',
            ],
            [
                'category_id' => 'VC003',
                'category_name' => 'Dress Code Violation',
                'description' => 'Non-compliance with uniform or attire rules.',
                'severity_level' => 'Low',
                'default_sanction' => 'Verbal Warning',
                'status' => 'Active',
            ],
            [
                'category_id' => 'VC004',
                'category_name' => 'Property Damage',
                'description' => 'Damaging school property intentionally.',
                'severity_level' => 'High',
                'default_sanction' => 'Suspension',
                'status' => 'Active',
            ],
            [
                'category_id' => 'VC005',
                'category_name' => 'Attendance Violation',
                'description' => 'Habitual tardiness or absences.',
                'severity_level' => 'Medium',
                'default_sanction' => 'Written Warning',
                'status' => 'Active',
            ],
        ];

        foreach ($data as $item) {
            ViolationCategory::create($item);
        }
    }
}
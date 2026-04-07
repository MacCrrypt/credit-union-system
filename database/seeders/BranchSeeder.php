<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            ['name' => 'Bamenda 1', 'location' => 'Bamenda'],
            ['name' => 'Bamenda 2', 'location' => 'Bamenda'],
            ['name' => 'Duala 3', 'location' => 'Duala'],
        ];

        foreach ($branches as $branch) {
            Branch::updateOrCreate([
                'name' => $branch['name'],
            ], [
                'location' => $branch['location'],
            ]);
        }
    }
}

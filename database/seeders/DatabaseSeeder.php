<?php

namespace Database\Seeders;

use App\Models\Grading;
use App\Models\GradingTerm;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate([
            'email' => 'admin@gmail.com',
        ], [
            'fullname' => 'AENHS REGISTRAR',
            'pwd' => hash('sha512', 'admin'),
            'utype' => 'admin',
        ]);

        $terms = ['First Grading', 'Second Grading', 'Third Grading', 'Fourth Grading'];
        foreach ($terms as $i => $term) {
            GradingTerm::firstOrCreate([
                'term_order' => $i + 1,
            ], [
                'term_name' => $term,
            ]);
        }

        $subjects = [
            'Filipino',
            'English',
            'Mathematics',
            'Science',
            'Araling Panlipunan (AP)',
            'Edukasyon sa Pagpapakatao (EsP)',
            'Technology and Livelihood Education (TLE)',
            'MAPEH',
            'Music',
            'Arts',
            'Physical Education',
            'Health',
        ];

        foreach ($subjects as $subject) {
            Subject::firstOrCreate(['subject' => $subject]);
        }

        Section::firstOrCreate(['grade_yr' => 'Grade 7', 'section' => 'Diamond']);
        Section::firstOrCreate(['grade_yr' => 'Grade 8', 'section' => 'Kingfisher']);

        SchoolYear::firstOrCreate(['sy' => '2023-2024']);
        Grading::updateOrCreate(['id' => 1], ['grading' => 'First Grading', 'sy' => '2023-2024']);
    }
}

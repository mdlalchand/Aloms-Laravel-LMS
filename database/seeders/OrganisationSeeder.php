<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Course;
use App\Models\Slider;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OrganisationSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        $branches = [
            ['name' => 'Dhanmondi Centre', 'code' => 'DHK-01', 'address' => 'House 42, Road 9/A, Dhanmondi, Dhaka 1209', 'phone' => '+880 1711-100001'],
            ['name' => 'Uttara Centre', 'code' => 'DHK-02', 'address' => 'Sector 7, Uttara Model Town, Dhaka 1230', 'phone' => '+880 1711-100002'],
            ['name' => 'Chattogram Centre', 'code' => 'CTG-01', 'address' => 'Nasirabad Road, Chattogram 4000', 'phone' => '+880 1711-100003'],
            ['name' => 'Online (National)', 'code' => 'ONL-01', 'address' => 'Live classes for students all over Bangladesh', 'phone' => '+880 1711-100004'],
        ];

        $models = collect($branches)->map(fn ($b) => Branch::firstOrCreate(
            ['code' => $b['code']],
            $b + ['email' => strtolower($b['code']).'@redwansmethod.test', 'is_active' => true]
        ));

        // Branch managers
        foreach ($models->take(3) as $i => $branch) {
            $manager = User::firstOrCreate(
                ['email' => 'manager'.($i + 1).'@lms.test'],
                [
                    'name' => ['Dhanmondi Manager', 'Uttara Manager', 'Chattogram Manager'][$i],
                    'password' => $password,
                    'role' => 'branch_manager',
                    'branch_id' => $branch->id,
                    'designation' => 'Branch Manager',
                    'phone' => '+880 1811-20000'.($i + 1),
                    'is_active' => true,
                ]
            );
            $branch->update(['manager_id' => $manager->id]);
        }

        // Assign existing students + instructors to branches
        $students = User::where('role', 'student')->orderBy('id')->get();
        foreach ($students as $i => $student) {
            $student->update(['branch_id' => $models[$i % $models->count()]->id]);
        }
        foreach (User::where('role', 'instructor')->get() as $i => $teacher) {
            $teacher->update(['branch_id' => $models[$i % $models->count()]->id]);
        }

        // Guardians linked to first two students
        $relations = ['father', 'mother'];
        foreach ($students->take(2) as $i => $student) {
            $guardian = User::firstOrCreate(
                ['email' => 'guardian'.($i + 1).'@lms.test'],
                [
                    'name' => $student->name.'\'s Guardian',
                    'password' => $password,
                    'role' => 'guardian',
                    'branch_id' => $student->branch_id,
                    'phone' => '+880 1911-30000'.($i + 1),
                    'is_active' => true,
                ]
            );
            $guardian->children()->syncWithoutDetaching([
                $student->id => ['relation' => $relations[$i] ?? 'guardian'],
            ]);
        }

        // Home page hero sliders
        $slides = [
            [
                'badge' => 'Admission 2026 — online & offline batches open',
                'title' => "Learn smarter with Redwan's Method",
                'subtitle' => "Class 9–10 (SSC), HSC and admission preparation with the country's best teachers. Video lessons, class notes, MCQ exams and lifetime access.",
                'image' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1600&q=80',
                'button_text' => 'Explore courses',
                'button_url' => '/courses',
                'theme' => 'emerald',
                'sort_order' => 1,
            ],
            [
                'badge' => 'HSC 2026 crash course',
                'title' => 'Finish the syllabus with exam-focused classes',
                'subtitle' => 'Physics, Chemistry, Higher Math and Biology — chapter-wise MCQ + CQ practice with weekly live doubt-solving sessions.',
                'image' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1600&q=80',
                'button_text' => 'See HSC courses',
                'button_url' => '/courses?category=hsc',
                'theme' => 'light',
                'sort_order' => 2,
            ],
            [
                'badge' => 'University admission',
                'title' => 'Engineering & medical admission preparation',
                'subtitle' => 'Full admission syllabus, model tests and previous-year question analysis — study from home at your own pace.',
                'image' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&w=1600&q=80',
                'button_text' => 'Browse admission batch',
                'button_url' => '/courses?category=admission',
                'theme' => 'slate',
                'sort_order' => 3,
            ],
        ];

        foreach ($slides as $slide) {
            Slider::updateOrCreate(['sort_order' => $slide['sort_order']], $slide + ['is_active' => true]);
        }

        // Attach a couple of live classes to the first course (if the teacher module is used)
        if ($course = Course::first()) {
            \App\Models\LiveClass::firstOrCreate(
                ['course_id' => $course->id, 'title' => 'Weekly doubt-solving session'],
                [
                    'platform' => 'jitsi',
                    'meeting_url' => 'https://meet.jit.si/redwans-method-demo',
                    'starts_at' => now()->addDays(2)->setTime(20, 0),
                    'duration_minutes' => 60,
                    'is_published' => true,
                ]
            );
        }
    }
}

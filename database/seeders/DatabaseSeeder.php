<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\Ebook;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\Lesson;
use App\Models\LiveClass;
use App\Models\Option;
use App\Models\Order;
use App\Models\Question;
use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /* ---------------- Users ---------------- */
        $admin = User::create([
            'name' => 'Redwan Hushen', 'email' => 'admin@lms.test', 'password' => Hash::make('password'),
            'role' => 'admin', 'phone' => '01700000001', 'designation' => 'Founder & Lead Instructor',
            'bio' => 'Maths teacher, author and founder of Redwan\'s Method.',
        ]);

        $instructors = collect([
            ['Nusrat Jahan', 'Physics', 'Senior Physics Instructor'],
            ['Tanvir Ahmed', 'Chemistry', 'Chemistry & Admission Coach'],
            ['Sadia Islam', 'Higher Math', 'HSC Higher Math Instructor'],
        ])->map(fn ($row, $i) => User::create([
            'name' => $row[0], 'email' => 'teacher'.$i.'@lms.test', 'password' => Hash::make('password'),
            'role' => 'instructor', 'designation' => $row[2], 'phone' => '0170000001'.$i,
            'bio' => $row[1].' instructor with 8+ years of teaching experience.',
        ]));

        $students = collect([
            'Tasannun Tarin', 'Mubina Khanom', 'Shamim Shohan', 'Afrina Jahan',
            'Borhan Uddin Shifat', 'Rakib Hasan', 'Sumaiya Akter', 'Naimur Rahman',
        ])->map(fn ($name, $i) => User::create([
            'name' => $name,
            'email' => $i === 0 ? 'student@lms.test' : 'student'.$i.'@lms.test',
            'password' => Hash::make('password'),
            'role' => 'student', 'phone' => '0180000000'.$i,
        ]));

        /* ---------------- Settings ---------------- */
        foreach ([
            'site_name' => "Redwan's Method",
            'site_tagline' => 'Quality education for everyone, everywhere.',
            'site_email' => 'support@redwansmethod.test',
            'site_phone' => '+880 1700-000000',
            'site_address' => 'Dhanmondi, Dhaka 1205, Bangladesh',
            'bkash_number' => '01711111111',
            'nagad_number' => '01722222222',
            'currency' => '৳',
            'facebook' => 'https://facebook.com/',
            'youtube' => 'https://youtube.com/',
            'instagram' => 'https://instagram.com/',
            'certificate_signature' => 'Redwan Hushen',
            'about_text' => 'A comprehensive online learning platform with courses delivered by highly qualified educators.',
        ] as $key => $value) {
            Setting::set($key, $value);
        }

        /* ---------------- Categories ---------------- */
        $categories = collect([
            ['Academic Courses', '📚', 'Comprehensive academic preparation for SSC & HSC students.'],
            ['Admission Courses', '🎓', 'Specialized courses for university admission tests.'],
            ['Mathematics', '➗', 'Advanced math concepts and problem solving.'],
            ['Science & Physics', '🔬', 'Physics, chemistry and biology made simple.'],
            ['Technology', '💻', 'Modern technology, programming and freelancing.'],
            ['Skill Development', '🚀', 'Communication, English and career skills.'],
        ])->map(fn ($row, $i) => Category::create([
            'name' => $row[0], 'icon' => $row[1], 'description' => $row[2],
            'position' => $i + 1, 'is_active' => true,
        ]));

        /* ---------------- Courses ---------------- */
        $courseBlueprints = [
            [
                'title' => 'SSC Higher Math Complete Course (Class 9-10)',
                'category' => 2, 'price' => 3500, 'discount' => 2500, 'featured' => true, 'level' => 'intermediate',
                'subtitle' => 'Chapter-wise full syllabus with board question solve and MCQ exams.',
                'outcomes' => ['Solve every board question of SSC Higher Math', 'Master trigonometry, geometry and calculus basics', 'Score 90+ with chapter-wise MCQ practice'],
                'sections' => [
                    'Chapter 1: Real Numbers' => ['Real numbers & surds', 'Exercise 1.1 solve', 'Board MCQ practice'],
                    'Chapter 2: Sets & Functions' => ['Sets explained', 'Functions and graphs', 'Class test 01'],
                    'Chapter 3: Algebraic Expressions' => ['Polynomials', 'Factorization tricks', 'Board question solve'],
                ],
            ],
            [
                'title' => 'Physics 1st Paper — HSC Full Course',
                'category' => 3, 'price' => 4000, 'discount' => 2990, 'featured' => true, 'level' => 'advanced',
                'subtitle' => 'Complete HSC Physics 1st paper with practical demonstrations.',
                'outcomes' => ['Understand every chapter of HSC Physics 1st paper', 'Solve CQ and MCQ with confidence', 'Practice with real exam standard tests'],
                'sections' => [
                    'Physical World & Measurement' => ['Units & dimensions', 'Errors in measurement'],
                    'Vectors' => ['Vector addition', 'Dot & cross product', 'Problem solve session'],
                    'Dynamics' => ['Newton\'s laws', 'Momentum & collision', 'MCQ drill'],
                ],
            ],
            [
                'title' => 'University Admission Crash Course (Engineering)',
                'category' => 1, 'price' => 6000, 'discount' => 4500, 'featured' => true, 'level' => 'advanced',
                'subtitle' => 'Intensive admission prep with 120+ model tests.',
                'outcomes' => ['Complete BUET/CKRUET syllabus revision', '120+ model tests with solutions', 'Time management for MCQ exams'],
                'sections' => [
                    'Math for Admission' => ['Shortcut techniques', 'Higher math selected problems'],
                    'Physics for Admission' => ['Selected problem solving', 'Concept clearing class'],
                    'Model Tests' => ['Model test 01 with solution', 'Model test 02 with solution'],
                ],
            ],
            [
                'title' => 'SSC Chemistry Foundation (Free)',
                'category' => 3, 'price' => 0, 'discount' => null, 'featured' => false, 'level' => 'beginner',
                'subtitle' => 'Free foundation batch — start chemistry from zero.',
                'outcomes' => ['Understand basic chemistry concepts', 'Prepare for SSC chemistry exam'],
                'sections' => [
                    'Introduction' => ['What is chemistry?', 'States of matter'],
                    'Periodic Table' => ['Groups & periods', 'Trends in the periodic table'],
                ],
            ],
            [
                'title' => 'English Grammar & Writing for HSC',
                'category' => 5, 'price' => 1500, 'discount' => null, 'featured' => false, 'level' => 'beginner',
                'subtitle' => 'Grammar rules, paragraph and composition writing made easy.',
                'outcomes' => ['Master all grammar rules for HSC', 'Write error-free paragraphs & letters'],
                'sections' => [
                    'Grammar' => ['Right form of verbs', 'Narration change', 'Modifiers'],
                    'Writing' => ['Paragraph writing', 'Completing story'],
                ],
            ],
            [
                'title' => 'Web Development with Laravel (Beginner)',
                'category' => 4, 'price' => 5000, 'discount' => 3500, 'featured' => false, 'level' => 'beginner',
                'subtitle' => 'Build real products with PHP, Laravel and MySQL.',
                'outcomes' => ['Build full-stack apps with Laravel', 'Understand MVC architecture', 'Deploy to shared hosting'],
                'sections' => [
                    'Getting Started' => ['PHP basics', 'Installing Laravel', 'Routing & controllers'],
                    'Database' => ['Migrations & models', 'Eloquent relationships'],
                    'Project' => ['Build an LMS', 'Deploy to production'],
                ],
            ],
        ];

        foreach ($courseBlueprints as $i => $bp) {
            $course = Course::create([
                'title' => $bp['title'],
                'subtitle' => $bp['subtitle'],
                'category_id' => $categories[$bp['category']]->id,
                'instructor_id' => $i % 2 === 0 ? $admin->id : $instructors[$i % $instructors->count()]->id,
                'description' => $bp['subtitle']."\n\nThis course is designed by the Redwan's Method academic team. You get recorded lessons, lecture notes, chapter-wise MCQ exams and live doubt-solving classes. Lifetime access, no expiry.",
                'requirements' => "Basic understanding of the previous class syllabus.\nA notebook and a stable internet connection.",
                'outcomes' => $bp['outcomes'],
                'price' => $bp['price'],
                'discount_price' => $bp['discount'],
                'level' => $bp['level'],
                'language' => 'Bangla',
                'is_published' => true,
                'is_featured' => $bp['featured'],
                'has_certificate' => true,
                'starts_at' => now()->addDays(3 + $i),
                'seat_limit' => 200,
            ]);

            $lessonPosition = 1;
            foreach ($bp['sections'] as $sectionTitle => $lessons) {
                $section = $course->sections()->create([
                    'title' => $sectionTitle,
                    'position' => $course->sections()->count() + 1,
                ]);

                foreach ($lessons as $j => $lessonTitle) {
                    Lesson::create([
                        'course_id' => $course->id,
                        'section_id' => $section->id,
                        'title' => $lessonTitle,
                        'type' => $j === count($lessons) - 1 && $j % 2 === 0 ? 'text' : 'video',
                        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'content' => "In this lesson we cover **{$lessonTitle}** step by step.\n\n1. Concept explanation\n2. Worked examples\n3. Practice problems with solutions\n\nDownload the attached note for practice.",
                        'duration_minutes' => rand(12, 55),
                        'position' => $lessonPosition++,
                        'is_free_preview' => $lessonPosition <= 3,
                        'is_published' => true,
                    ]);
                }
            }

            $course->update(['duration_minutes' => $course->lessons()->sum('duration_minutes')]);

            // Course exams
            $exam = Exam::create([
                'course_id' => $course->id,
                'title' => 'Chapter-wise MCQ Test — '.Str::words($course->title, 4, ''),
                'description' => 'Answer all questions. Each correct answer gives 1 mark, each wrong answer deducts 0.25 marks.',
                'duration_minutes' => 20,
                'pass_marks' => 6,
                'negative_marking' => 0.25,
                'is_published' => true,
                'is_free' => false,
                'shuffle_questions' => true,
            ]);

            $this->seedQuestions($exam);

            // Announcement + live class
            Announcement::create([
                'course_id' => $course->id,
                'title' => 'Welcome to '.Str::words($course->title, 3, ''),
                'body' => 'Class routine has been published. Live class every Friday at 8 PM. Complete the first chapter before the live session.',
                'is_published' => true,
            ]);

            LiveClass::create([
                'course_id' => $course->id,
                'title' => 'Doubt solving: '.Str::limit($course->title, 38),
                'meeting_url' => 'https://zoom.us/j/123456789',
                'platform' => 'zoom',
                'starts_at' => now()->addDays(2),
                'duration_minutes' => 90,
            ]);
        }

        /* ---------------- E-books ---------------- */
        $books = [
            ['SSC Higher Math Solution Book', 350, 240, false],
            ['HSC Physics Formula Sheet', 0, 24, true],
            ['Admission Model Test Book (Engineering)', 500, 320, false],
            ['English Grammar Cheat Sheet', 0, 18, true],
        ];
        foreach ($books as $b) {
            Ebook::create([
                'title' => $b[0], 'description' => 'A carefully prepared resource by Redwan\'s Method academic team. Includes solved examples, shortcuts and practice questions.',
                'price' => $b[1], 'pages' => $b[2], 'is_free' => $b[3], 'is_published' => true,
            ]);
        }

        /* ---------------- Coupons ---------------- */
        Coupon::create(['code' => 'EID25', 'type' => 'percent', 'value' => 25, 'min_amount' => 1000, 'usage_limit' => 100, 'is_active' => true]);
        Coupon::create(['code' => 'FLAT500', 'type' => 'fixed', 'value' => 500, 'min_amount' => 2000, 'usage_limit' => 0, 'is_active' => true]);

        /* ---------------- Enrollments, orders & reviews ---------------- */
        $allCourses = Course::all();
        foreach ($students as $index => $student) {
            $picked = $allCourses->shuffle()->take(rand(1, 3));

            foreach ($picked as $course) {
                $price = $course->final_price;

                $order = Order::create([
                    'user_id' => $student->id,
                    'subtotal' => $price,
                    'discount' => 0,
                    'total' => $price,
                    'status' => 'paid',
                    'name' => $student->name,
                    'phone' => $student->phone,
                    'email' => $student->email,
                ]);
                $order->items()->create([
                    'item_type' => 'course', 'item_id' => $course->id,
                    'title' => $course->title, 'price' => $price, 'qty' => 1,
                ]);
                $order->payments()->create([
                    'gateway' => 'bkash', 'transaction_id' => 'TRX'.Str::upper(Str::random(8)),
                    'sender_number' => $student->phone, 'amount' => $price, 'status' => 'verified',
                ]);

                Enrollment::create([
                    'user_id' => $student->id, 'course_id' => $course->id, 'order_id' => $order->id,
                    'price_paid' => $price, 'status' => 'active', 'enrolled_at' => now()->subDays(rand(1, 40)),
                ]);

                // mark a few lessons complete
                $lessons = $course->lessons()->take(rand(0, 4))->get();
                foreach ($lessons as $lesson) {
                    \App\Models\LessonProgress::create([
                        'user_id' => $student->id, 'lesson_id' => $lesson->id,
                        'watched_seconds' => rand(120, 900), 'completed_at' => now()->subDays(rand(1, 20)),
                    ]);
                }

                Review::create([
                    'course_id' => $course->id,
                    'user_id' => $student->id,
                    'rating' => rand(4, 5),
                    'comment' => collect([
                        'ভাইয়ার ক্লাসগুলো amazing! প্রতিটা অধ্যায় এত সুন্দরভাবে বুঝিয়ে দিয়েছেন যে আর ভয় লাগে না।',
                        'To be honest, this series is too much effective. Before I was weak in MCQ, now I get 27-29 out of 30.',
                        'এত সুন্দর করে পড়ানোর জন্য ধন্যবাদ। CQ এবং MCQ দুটোই কভার করা হয়েছে।',
                        'Best platform for SSC preparation. The notes and model tests are worth every taka.',
                        'অসাধারণ! এত কম খরচে এত ভালো কোর্স আশা করিনি।',
                    ])->random(),
                    'is_approved' => true,
                ]);
            }
        }

        // One pending review for the moderation demo
        Review::create([
            'course_id' => $allCourses->first()->id,
            'user_id' => $students->last()->id,
            'rating' => 5,
            'comment' => 'Just finished chapter 2 — the explanation was superb!',
            'is_approved' => false,
        ]);

        // Pending manual payment order
        $pending = Order::create([
            'user_id' => $students->first()->id,
            'subtotal' => 2990, 'discount' => 0, 'total' => 2990,
            'status' => 'pending', 'name' => $students->first()->name,
            'phone' => $students->first()->phone, 'email' => $students->first()->email,
        ]);
        $pending->items()->create([
            'item_type' => 'course', 'item_id' => 2, 'title' => 'Physics 1st Paper — HSC Full Course',
            'price' => 2990, 'qty' => 1,
        ]);
        $pending->payments()->create([
            'gateway' => 'bkash', 'transaction_id' => 'TRXDEMO123',
            'sender_number' => '01800000000', 'amount' => 2990, 'status' => 'pending',
        ]);

        // A certificate for a fully completed student
        $done = Course::find($allCourses->first()->id);
        $student = $students->first();
        foreach ($done->lessons as $lesson) {
            \App\Models\LessonProgress::firstOrCreate(
                ['user_id' => $student->id, 'lesson_id' => $lesson->id],
                ['watched_seconds' => 600, 'completed_at' => now()->subDays(2)]
            );
        }
        \App\Models\Certificate::create([
            'user_id' => $student->id, 'course_id' => $done->id,
            'code' => 'CERT-DEMO1234', 'issued_at' => now()->subDay(),
        ]);

        $this->call(OrganisationSeeder::class);
    }

    private function seedQuestions(Exam $exam): void
    {
        $bank = [
            ['Which of the following is an irrational number?', ['√2', '0.25', '3/4', '0.333…'], 0,
             '√2 cannot be written as a fraction p/q, so it is irrational.'],
            ['If x + 1/x = 3, what is x² + 1/x²?', ['7', '9', '11', '6'], 0,
             'x² + 1/x² = (x + 1/x)² − 2 = 9 − 2 = 7.'],
            ['The SI unit of force is —', ['Newton', 'Joule', 'Watt', 'Pascal'], 0,
             'Force is measured in Newton (N).'],
            ['sin²θ + cos²θ = ?', ['1', '0', '2', 'tanθ'], 0,
             'This is the fundamental trigonometric identity.'],
            ['Which gas is most abundant in the atmosphere?', ['Nitrogen', 'Oxygen', 'Carbon dioxide', 'Argon'], 0,
             'Nitrogen makes up about 78% of the atmosphere.'],
            ['The derivative of sin x is —', ['cos x', '−cos x', 'sin x', '−sin x'], 0,
             'd/dx(sin x) = cos x.'],
            ['A body moving with uniform velocity has acceleration —', ['zero', 'constant', 'increasing', 'decreasing'], 0,
             'Uniform velocity means no change in velocity, so acceleration is zero.'],
            ['The value of log₁₀ 1000 is —', ['3', '2', '10', '100'], 0,
             '10³ = 1000, so log₁₀1000 = 3.'],
        ];

        shuffle($bank);

        foreach (array_slice($bank, 0, 8) as $i => $row) {
            $question = Question::create([
                'exam_id' => $exam->id,
                'type' => 'mcq',
                'question' => $row[0],
                'explanation' => $row[2],
                'marks' => 1,
                'position' => $i + 1,
            ]);

            $options = $row[1];
            shuffle($options);

            foreach ($options as $index => $text) {
                Option::create([
                    'question_id' => $question->id,
                    'option_text' => $text,
                    'is_correct' => $text === $row[1][0],
                    'position' => $index,
                ]);
            }
        }

        $exam->update(['total_marks' => $exam->questions()->sum('marks')]);
    }
}

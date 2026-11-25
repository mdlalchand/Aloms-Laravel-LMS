<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;

class CertificateController extends Controller
{
    public function show(Course $course)
    {
        $user = auth()->user();
        abort_unless($user->hasCourse($course), 403);

        $certificate = Certificate::where('user_id', $user->id)->where('course_id', $course->id)->first();

        abort_unless(
            $certificate || $course->progressFor($user) >= 100,
            403,
            'Finish the course to unlock your certificate.'
        );

        $certificate ??= Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'issued_at' => now(),
        ]);

        return view('student.certificate', compact('certificate', 'course'));
    }
}

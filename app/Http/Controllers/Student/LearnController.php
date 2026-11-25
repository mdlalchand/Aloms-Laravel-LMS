<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Course as CourseModel;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Services\OrderService;
use Illuminate\Http\Request;

class LearnController extends Controller
{
    private function guard(Course $course): void
    {
        $user = auth()->user();
        abort_unless($user->hasCourse($course) || $user->isAdmin(), 403, 'You are not enrolled in this course.');
    }

    public function show(Course $course)
    {
        $this->guard($course);
        $lesson = $course->nextLessonFor(auth()->user());
        abort_if(! $lesson, 404, 'This course has no lessons yet.');

        return redirect()->route('student.learn.lesson', [$course->slug, $lesson->id]);
    }

    public function lesson(Course $course, Lesson $lesson)
    {
        $this->guard($course);
        abort_unless($lesson->course_id === $course->id, 404);

        $user = auth()->user();
        $course->load(['sections.lessons', 'announcements', 'instructor']);
        $flat = $course->lessons()->get();
        $index = $flat->search(fn ($l) => $l->id === $lesson->id);

        LessonProgress::firstOrCreate(['user_id' => $user->id, 'lesson_id' => $lesson->id]);

        return view('student.learn', [
            'course' => $course,
            'lesson' => $lesson,
            'prev' => $index > 0 ? $flat[$index - 1] : null,
            'next' => $index < $flat->count() - 1 ? $flat[$index + 1] : null,
            'completedIds' => LessonProgress::where('user_id', $user->id)
                ->whereNotNull('completed_at')->pluck('lesson_id')->all(),
            'progress' => $course->progressFor($user),
            'exams' => $lesson->exams()->where('is_published', true)->get(),
            'announcements' => $course->announcements()->where('is_published', true)->latest()->take(3)->get(),
        ]);
    }

    public function complete(Course $course, Lesson $lesson)
    {
        $this->guard($course);
        $user = auth()->user();

        LessonProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['completed_at' => now()]
        );

        $certificate = OrderService::maybeIssueCertificate($user->id, $course);
        $next = $course->nextLessonFor($user);

        $redirect = $next && $next->id !== $lesson->id
            ? redirect()->route('student.learn.lesson', [$course->slug, $next->id])
            : redirect()->route('student.learn.lesson', [$course->slug, $lesson->id]);

        if ($course->progressFor($user) >= 100) {
            return $redirect->with('success', 'Congratulations, you finished the course! 🎉');
        }

        return $redirect->with('success', $certificate ? 'Lesson completed. Certificate unlocked!' : 'Lesson completed.');
    }

    /** AJAX heartbeat used by the video player to store watch time. */
    public function heartbeat(Request $request, Course $course, Lesson $lesson)
    {
        $this->guard($course);
        $seconds = (int) $request->input('seconds', 0);

        LessonProgress::updateOrCreate(
            ['user_id' => auth()->id(), 'lesson_id' => $lesson->id],
            ['watched_seconds' => $seconds]
        );

        return response()->json(['ok' => true]);
    }
}

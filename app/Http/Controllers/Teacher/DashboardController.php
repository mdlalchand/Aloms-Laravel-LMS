<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\LiveClass;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /** Course ids owned by the signed-in teacher (admins fall back to everything). */
    private function scopeIds(User $user): array
    {
        return Course::query()
            ->when(! $user->isAdmin(), fn ($q) => $q->where('instructor_id', $user->id))
            ->pluck('id')->all();
    }

    public function index()
    {
        $user = auth()->user();
        $ids = $this->scopeIds($user);

        $enrollments = Enrollment::whereIn('course_id', $ids);

        $stats = [
            'courses' => count($ids),
            'students' => (clone $enrollments)->distinct('user_id')->count('user_id'),
            'active' => (clone $enrollments)->where('status', 'active')->count(),
            'revenue' => (float) (clone $enrollments)->sum('price_paid'),
            'rating' => $ids ? round((float) Review::whereIn('course_id', $ids)->where('is_approved', true)->avg('rating'), 1) : 0.0,
            'exams' => Exam::whereIn('course_id', $ids)->count(),
        ];

        $recent = (clone $enrollments)->with(['user', 'course'])
            ->latest()->take(8)->get();

        $courses = Course::whereIn('id', $ids)
            ->withCount(['enrollments', 'lessons'])
            ->orderByDesc('enrollments_count')->take(5)->get();

        $upcoming = LiveClass::whereIn('course_id', $ids)
            ->where('starts_at', '>=', now()->subMinutes(30))
            ->orderBy('starts_at')->take(5)->get();

        // 7-day enrolment trend for the little chart
        $trend = collect(range(6, 0))->map(function ($d) use ($ids) {
            $day = now()->subDays($d);
            return [
                'label' => $day->format('D'),
                'count' => Enrollment::whereIn('course_id', $ids)->whereDate('created_at', $day->toDateString())->count(),
            ];
        });

        return view('teacher.dashboard', compact('stats', 'recent', 'courses', 'upcoming', 'trend'));
    }

    public function courses()
    {
        $ids = $this->scopeIds(auth()->user());

        return view('teacher.courses', [
            'courses' => Course::whereIn('id', $ids)->with('category')
                ->withCount(['enrollments', 'lessons', 'exams', 'reviews'])
                ->latest()->paginate(12),
        ]);
    }

    public function students(Request $request)
    {
        $ids = $this->scopeIds(auth()->user());

        $students = User::query()
            ->where('role', 'student')
            ->whereHas('enrollments', fn ($q) => $q->whereIn('course_id', $ids))
            ->when($request->get('q'), fn ($q, $s) => $q->where(fn ($w) => $w->where('name', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%")))
            ->withCount(['enrollments' => fn ($q) => $q->whereIn('course_id', $ids)])
            ->orderBy('name')->paginate(20)->withQueryString();

        // attach best exam score per student for quick insight
        $scores = ExamAttempt::whereIn('exam_id', Exam::whereIn('course_id', $ids)->pluck('id'))
            ->whereIn('user_id', $students->pluck('id'))
            ->select('user_id', DB::raw('AVG(score) as avg_score'))
            ->groupBy('user_id')->pluck('avg_score', 'user_id');

        return view('teacher.students', compact('students', 'scores'));
    }

    public function student(User $user)
    {
        abort_unless($user->isStudent(), 404);
        $ids = $this->scopeIds(auth()->user());

        $enrollments = $user->enrollments()->whereIn('course_id', $ids)->with('course')->get();
        abort_if($enrollments->isEmpty(), 403, 'This student is not in your courses.');

        return view('teacher.student-show', [
            'student' => $user,
            'enrollments' => $enrollments,
            'attempts' => $user->examAttempts()->whereHas('exam', fn ($q) => $q->whereIn('course_id', $ids))
                ->with('exam')->latest()->take(10)->get(),
            'certificates' => $user->certificates()->whereIn('course_id', $ids)->with('course')->get(),
        ]);
    }

    public function earnings()
    {
        $ids = $this->scopeIds(auth()->user());

        $months = collect(range(5, 0))->map(function ($m) use ($ids) {
            $start = now()->subMonths($m)->startOfMonth();
            return [
                'label' => $start->format('M'),
                'amount' => (float) Enrollment::whereIn('course_id', $ids)
                    ->whereBetween('created_at', [$start, $start->copy()->endOfMonth()])->sum('price_paid'),
            ];
        });

        return view('teacher.earnings', [
            'months' => $months,
            'total' => (float) Enrollment::whereIn('course_id', $ids)->sum('price_paid'),
            'rows' => Enrollment::whereIn('course_id', $ids)->with(['user', 'course'])
                ->latest()->paginate(20),
        ]);
    }

    public function liveClasses()
    {
        $ids = $this->scopeIds(auth()->user());

        return view('teacher.live-classes', [
            'classes' => LiveClass::whereIn('course_id', $ids)->with('course')
                ->orderByDesc('starts_at')->paginate(15),
        ]);
    }
}

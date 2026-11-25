<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\EbookPurchase;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\LessonProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $enrollments = Enrollment::with(['course.instructor', 'course.category'])
            ->where('user_id', $user->id)->latest()->get();

        $inProgress = $enrollments->filter(fn ($e) => $e->course && $e->course->progressFor($user) < 100);
        $completed = $enrollments->filter(fn ($e) => $e->course && $e->course->progressFor($user) >= 100);

        return view('student.dashboard', [
            'enrollments' => $enrollments->take(6),
            'continueCourse' => $inProgress->first(),
            'stats' => [
                'enrolled' => $enrollments->count(),
                'in_progress' => $inProgress->count(),
                'completed' => $completed->count(),
                'certificates' => $user->certificates()->count(),
                'lessons_done' => LessonProgress::where('user_id', $user->id)->whereNotNull('completed_at')->count(),
                'exam_attempts' => $user->examAttempts()->count(),
                'avg_score' => round((float) $user->examAttempts()->avg('score'), 1),
            ],
            'upcomingExams' => Exam::with('course')->where('is_published', true)
                ->whereIn('course_id', $enrollments->pluck('course_id'))
                ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '>=', now()->subDay()))
                ->orderBy('starts_at')->take(5)->get(),
            'recentLessons' => LessonProgress::with('lesson.course')->where('user_id', $user->id)
                ->latest('updated_at')->take(5)->get(),
        ]);
    }

    public function courses()
    {
        $user = auth()->user();
        return view('student.courses', [
            'enrollments' => Enrollment::with('course.instructor')->where('user_id', $user->id)->latest()->get(),
        ]);
    }

    public function orders()
    {
        return view('student.orders', [
            'orders' => auth()->user()->orders()->with('items', 'payments')->latest()->get(),
        ]);
    }

    public function ebooks()
    {
        return view('student.ebooks', [
            'purchases' => EbookPurchase::with('ebook')->where('user_id', auth()->id())->latest()->get(),
        ]);
    }

    public function certificates()
    {
        return view('student.certificates', [
            'certificates' => auth()->user()->certificates()->with('course')->latest()->get(),
        ]);
    }

    public function profile()
    {
        return view('student.profile', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return back()->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if (! Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        auth()->user()->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password changed.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Enrollment::with(['user', 'course'])->latest();

        if ($q = $request->get('q')) {
            $query->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%"))
                ->orWhereHas('course', fn ($c) => $c->where('title', 'like', "%{$q}%"));
        }

        if (! auth()->user()->isAdmin()) {
            $query->whereHas('course', fn ($c) => $c->where('instructor_id', auth()->id()));
        }

        return view('admin.enrollments.index', [
            'enrollments' => $query->paginate(20)->withQueryString(),
            'courses' => Course::orderBy('title')->get(['id', 'title']),
            'students' => User::where('role', 'student')->orderBy('name')->get(['id', 'name', 'email']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        Enrollment::firstOrCreate(
            $data,
            ['price_paid' => 0, 'status' => 'active', 'enrolled_at' => now()]
        );

        return back()->with('success', 'Student enrolled manually.');
    }

    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();
        return back()->with('success', 'Enrollment removed.');
    }
}

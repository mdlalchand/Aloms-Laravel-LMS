<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /** The branch this manager is responsible for. */
    private function branch(): Branch
    {
        $user = auth()->user();

        $branch = $user->branch
            ?? Branch::where('manager_id', $user->id)->first();

        abort_unless($branch, 403, 'No branch has been assigned to your account yet. Please contact the admin.');

        return $branch;
    }

    public function index()
    {
        $branch = $this->branch();
        $studentIds = $branch->users()->where('role', 'student')->pluck('id');

        $stats = [
            'students' => $studentIds->count(),
            'teachers' => $branch->users()->where('role', 'instructor')->count(),
            'enrollments' => Enrollment::whereIn('user_id', $studentIds)->count(),
            'revenue' => (float) Order::where('status', 'paid')->whereIn('user_id', $studentIds)->sum('total'),
            'pending' => (float) Order::where('status', 'pending')->whereIn('user_id', $studentIds)->sum('total'),
        ];

        $months = collect(range(5, 0))->map(function ($m) use ($studentIds) {
            $start = now()->subMonths($m)->startOfMonth();
            return [
                'label' => $start->format('M'),
                'amount' => (float) Order::where('status', 'paid')->whereIn('user_id', $studentIds)
                    ->whereBetween('created_at', [$start, $start->copy()->endOfMonth()])->sum('total'),
            ];
        });

        $topCourses = DB::table('enrollments')
            ->join('courses', 'courses.id', '=', 'enrollments.course_id')
            ->whereIn('enrollments.user_id', $studentIds)
            ->select('courses.title', DB::raw('COUNT(*) as total'), DB::raw('SUM(enrollments.price_paid) as revenue'))
            ->groupBy('courses.id', 'courses.title')
            ->orderByDesc('total')->take(5)->get();

        return view('branch.dashboard', [
            'branch' => $branch,
            'stats' => $stats,
            'months' => $months,
            'topCourses' => $topCourses,
            'recentStudents' => $branch->users()->where('role', 'student')->latest()->take(8)->get(),
            'recentOrders' => Order::whereIn('user_id', $studentIds)->with('user')->latest()->take(8)->get(),
        ]);
    }

    public function students(Request $request)
    {
        $branch = $this->branch();

        $students = $branch->users()->where('role', 'student')
            ->when($request->get('q'), fn ($q, $s) => $q->where(fn ($w) => $w->where('name', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%")))
            ->withCount(['enrollments', 'orders'])
            ->orderBy('name')->paginate(20)->withQueryString();

        return view('branch.students', ['branch' => $branch, 'students' => $students]);
    }

    public function student(User $student)
    {
        $branch = $this->branch();
        abort_unless($student->branch_id === $branch->id, 403, 'This student belongs to another branch.');

        return view('branch.student-show', [
            'branch' => $branch,
            'student' => $student,
            'enrollments' => $student->enrollments()->with('course')->get(),
            'orders' => $student->orders()->with('items')->latest()->take(10)->get(),
            'attempts' => $student->examAttempts()->with('exam')->latest()->take(10)->get(),
            'progress' => $student->overallProgress(),
        ]);
    }

    public function staff()
    {
        $branch = $this->branch();

        return view('branch.staff', [
            'branch' => $branch,
            'staff' => $branch->users()->whereIn('role', ['instructor', 'branch_manager'])->orderBy('name')->get(),
        ]);
    }

    public function revenue()
    {
        $branch = $this->branch();
        $studentIds = $branch->users()->where('role', 'student')->pluck('id');

        return view('branch.revenue', [
            'branch' => $branch,
            'orders' => Order::whereIn('user_id', $studentIds)->with(['user', 'items'])
                ->latest()->paginate(20),
            'total' => (float) Order::where('status', 'paid')->whereIn('user_id', $studentIds)->sum('total'),
        ]);
    }
}

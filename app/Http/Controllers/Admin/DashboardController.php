<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\ExamAttempt;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $revenue = Order::where('status', 'paid')->sum('total');

        $monthly = Order::where('status', 'paid')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("strftime('%Y-%m', created_at) as ym, SUM(total) as total")
            ->groupBy('ym')->pluck('total', 'ym');

        $labels = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i));
        $chart = [
            'labels' => $labels->map(fn ($d) => $d->format('M'))->all(),
            'values' => $labels->map(fn ($d) => (float) ($monthly[$d->format('Y-m')] ?? 0))->all(),
        ];

        return view('admin.dashboard', [
            'stats' => [
                'revenue' => (float) $revenue,
                'orders' => Order::count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'students' => User::where('role', 'student')->count(),
                'courses' => Course::count(),
                'published' => Course::where('is_published', true)->count(),
                'enrollments' => Enrollment::count(),
                'instructors' => User::where('role', 'instructor')->count(),
                'categories' => Category::count(),
                'attempts' => ExamAttempt::where('status', 'submitted')->count(),
                'avg_score' => round((float) ExamAttempt::where('status', 'submitted')->avg('score'), 1),
                'pending_reviews' => Review::where('is_approved', false)->count(),
            ],
            'chart' => $chart,
            'recentOrders' => Order::with('user')->latest()->take(6)->get(),
            'recentEnrollments' => Enrollment::with(['user', 'course'])->latest()->take(6)->get(),
            'topCourses' => Course::withCount('enrollments')->orderByDesc('enrollments_count')->take(5)->get(),
            'pendingReviews' => Review::with(['user', 'course'])->where('is_approved', false)->latest()->take(5)->get(),
        ]);
    }
}

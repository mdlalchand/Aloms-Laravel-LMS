<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $guardian = auth()->user();
        $children = $guardian->children()->with('branch')->get();

        $summary = $children->map(fn (User $child) => [
            'child' => $child,
            'courses' => $child->enrollments()->count(),
            'progress' => $child->overallProgress(),
            'avg_score' => round((float) $child->examAttempts()->avg('score'), 1),
            'exams' => $child->examAttempts()->count(),
            'certificates' => $child->certificates()->count(),
            'due' => (float) $child->orders()->where('status', 'pending')->sum('total'),
        ]);

        return view('guardian.dashboard', [
            'summary' => $summary,
            'orders' => Order::whereIn('user_id', $children->pluck('id'))
                ->with(['user', 'items'])->latest()->take(8)->get(),
        ]);
    }

    /** Detailed report card for one linked child. */
    public function child(User $child)
    {
        $guardian = auth()->user();
        $linked = $guardian->children()->whereKey($child->id)->exists();
        abort_unless($linked || $guardian->isAdmin(), 403, 'This student is not linked to your account.');

        return view('guardian.child', [
            'child' => $child->load('branch'),
            'enrollments' => $child->enrollments()->with('course')->get(),
            'attempts' => $child->examAttempts()->with('exam.course')->latest()->take(15)->get(),
            'certificates' => $child->certificates()->with('course')->get(),
            'orders' => $child->orders()->with('items')->latest()->take(10)->get(),
            'progress' => $child->overallProgress(),
        ]);
    }
}

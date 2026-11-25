<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Review;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::published()->with(['instructor', 'category']);

        if ($q = $request->get('q')) {
            $query->where(fn ($w) => $w->where('title', 'like', "%{$q}%")
                ->orWhere('subtitle', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%"));
        }

        if ($category = $request->get('category')) {
            $query->whereHas('category', fn ($c) => $c->where('slug', $category));
        }

        if ($level = $request->get('level')) {
            $query->where('level', $level);
        }

        match ($request->get('sort')) {
            'price_low' => $query->orderByRaw('COALESCE(discount_price, price) asc'),
            'price_high' => $query->orderByRaw('COALESCE(discount_price, price) desc'),
            'popular' => $query->withCount('enrollments')->orderByDesc('enrollments_count'),
            'rating' => $query->withAvg(['reviews as rating_avg' => fn ($r) => $r->where('is_approved', true)], 'rating')->orderByDesc('rating_avg'),
            default => $query->latest(),
        };

        return view('courses.index', [
            'courses' => $query->paginate(9)->withQueryString(),
            'categories' => Category::where('is_active', true)->withCount('publishedCourses')->get(),
            'filters' => $request->only('q', 'category', 'level', 'sort'),
        ]);
    }

    public function category(Category $category)
    {
        return view('courses.index', [
            'courses' => Course::published()->where('category_id', $category->id)
                ->with(['instructor', 'category'])->latest()->paginate(9),
            'categories' => Category::where('is_active', true)->withCount('publishedCourses')->get(),
            'filters' => ['category' => $category->slug],
            'activeCategory' => $category,
        ]);
    }

    public function show(Request $request, Course $course)
    {
        abort_unless($course->is_published || optional($request->user())->isAdmin(), 404);

        $course->load(['sections.lessons', 'instructor', 'category', 'liveClasses']);
        $user = $request->user();
        $enrolled = $user && $user->hasCourse($course);

        return view('courses.show', [
            'course' => $course,
            'enrolled' => $enrolled,
            'reviews' => $course->reviews()->with('user')->where('is_approved', true)->latest()->take(10)->get(),
            'related' => Course::published()->where('category_id', $course->category_id)
                ->where('id', '!=', $course->id)->take(3)->get(),
            'myReview' => $user ? $course->reviews()->where('user_id', $user->id)->first() : null,
            'exams' => $course->exams()->where('is_published', true)->get(),
        ]);
    }

    /** Free enrollment (price = 0). */
    public function enrollFree(Request $request, Course $course)
    {
        abort_unless($course->final_price <= 0, 403);

        Enrollment::firstOrCreate(
            ['user_id' => $request->user()->id, 'course_id' => $course->id],
            ['price_paid' => 0, 'status' => 'active', 'enrolled_at' => now()]
        );

        return redirect()->route('student.learn', $course->slug)
            ->with('success', 'You are enrolled. Happy learning!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\Ebook;
use App\Models\Review;
use App\Models\Slider;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        return view('home.index', [
            'sliders' => Slider::where('is_active', true)->orderBy('sort_order')->get(),
            'categories' => Category::where('is_active', true)->whereNull('parent_id')->orderBy('position')->get(),
            'featured' => Course::published()->featured()->with(['instructor', 'category'])->latest()->take(6)->get(),
            'latest' => Course::published()->with(['instructor', 'category'])->latest()->take(8)->get(),
            'ebooks' => Ebook::where('is_published', true)->latest()->take(4)->get(),
            'reviews' => Review::with(['user', 'course'])->where('is_approved', true)->latest()->take(6)->get(),
            'stats' => [
                'students' => User::where('role', 'student')->count(),
                'courses' => Course::published()->count(),
                'instructors' => User::where('role', 'instructor')->count(),
                'lessons' => \App\Models\Lesson::count(),
            ],
        ]);
    }
}

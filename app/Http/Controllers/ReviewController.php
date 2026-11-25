<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $request->user()->reviews()->updateOrCreate(
            ['course_id' => $course->id],
            $data + ['is_approved' => false]
        );

        return back()->with('success', 'Thanks! Your review is pending approval.');
    }
}

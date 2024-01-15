<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    function filterAndSortCourses(Request $request)
    {
        $category = $request->input('category');
        $type = $request->input('type');

        $query = Course::query();

        if ($category) {
            $query = $query->whereHas('category', function ($query) use ($category) {
                $query->where('name', $category);
            });
        }

        if ($type) {
            $query = $query->whereHas('type', function ($query) use ($type) {
                $query->where('name', $type);
            });
            if ($type == 'free') {
                $query = $query->where('price', 0);
            }
            if ($type == 'no-free') {
                $query = $query->where('price', '>', 0);
            }
        }

        $sort = $request->input('sort');
        if ($sort == 'newest') {
            $query = $query->orderBy('created_at', 'desc');
        } elseif ($sort == 'oldest') {
            $query = $query->orderBy('created_at', 'asc');
        }

        $courses = $query->get();
        return view('courses.index', compact('courses'));
    }
}

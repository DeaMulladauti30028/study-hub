<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::latest()->paginate(10);
        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        return view('courses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'code'  => ['required','string','max:64','unique:courses,code'],
            'description' => ['nullable','string'],
        ]);

        Course::create($data);

        return redirect()->route('courses.index')->with('status', 'Course created!');
    }
}

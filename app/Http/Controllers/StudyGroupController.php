<?php

namespace App\Http\Controllers;

use App\Models\StudyGroup;
use App\Models\Course;
use Illuminate\Http\Request;

class StudyGroupController extends Controller
{
    public function index()
    {
        $groups = StudyGroup::with('course')->latest()->paginate(10);
        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        $courses = Course::orderBy('title')->get(['id','title','code']);
        return view('groups.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id'   => ['required','exists:courses,id'],
            'name'        => ['required','string','max:255'],
            'description' => ['nullable','string'],
        ]);

        StudyGroup::create($data);

        return redirect()->route('groups.index')->with('status', 'Study group created!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\StudyGroup;
use App\Models\Course;
use Illuminate\Http\Request;

class StudyGroupController extends Controller
{
    public function index(Request $request)
{
    $onlyUpcoming = $request->boolean('upcoming');

    $groups = StudyGroup::with(['course', 'nextSession'])
        ->withCount('members')
        ->when($onlyUpcoming, function ($q) {
            $q->whereHas('sessions', function ($q2) {
                $q2->where('starts_at', '>=', now());
            });
        })
        ->latest()
        ->paginate(10)
        ->withQueryString(); 

    $myGroupIds = auth()->user()->studyGroups()->pluck('study_groups.id')->toArray();

    return view('groups.index', compact('groups', 'myGroupIds', 'onlyUpcoming'));
}

    public function join(StudyGroup $group)
{
    auth()->user()->studyGroups()->syncWithoutDetaching([$group->id]);
    return back()->with('status', 'Joined the group!');
}

public function leave(StudyGroup $group)
{
    auth()->user()->studyGroups()->detach($group->id);
    return back()->with('status', 'Left the group!');
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

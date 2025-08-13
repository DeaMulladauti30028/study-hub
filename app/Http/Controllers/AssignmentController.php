<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\StudyGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AssignmentController extends Controller
{
    public function index(StudyGroup $group)
    {
        $isMember = auth()->user()->studyGroups()->whereKey($group->id)->exists();

        $assignments = $group->assignments()
            ->orderByRaw('due_at IS NULL')   // due_at null last
            ->orderBy('due_at', 'asc')
            ->paginate(10);

        return view('assignments.index', compact('group','assignments','isMember'));
    }

    public function create(StudyGroup $group)
    {
        abort_unless(auth()->user()->studyGroups()->whereKey($group->id)->exists(), 403);
        return view('assignments.create', compact('group'));
    }

    public function store(Request $request, StudyGroup $group)
    {
        if (! auth()->user()->studyGroups()->whereKey($group->id)->exists()) {
            return redirect()->route('groups.assignments.index', $group)
                ->with('status', 'You must join this group to add assignments.');
        }

        $data = $request->validate([
            'title'       => ['required','string','max:255'],
            'due_at'      => ['nullable','date'],
            'description' => ['nullable','string'],
        ]);

        if (!empty($data['due_at'])) {
            $data['due_at'] = Carbon::parse($data['due_at']);
        }
        $data['study_group_id'] = $group->id;

        Assignment::create($data);

        return redirect()->route('groups.assignments.index', $group)->with('status', 'Assignment created!');
    }
}

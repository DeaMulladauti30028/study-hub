<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\StudyGroup;
use Illuminate\Http\Request;

class GroupAssignmentController extends Controller
{
    public function index(StudyGroup $group, Request $req)
    {
        $this->authorize('viewAny', [Assignment::class, $group]);

        $query = Assignment::query()->where('study_group_id', $group->id);

        // Search
        if ($s = trim($req->get('q', ''))) {
            $query->where('title', 'like', "%{$s}%");
        }

        // Filters: all/mine/done/pending
        $filter = $req->get('filter', 'all');
        $user   = $req->user();

        if ($filter === 'mine') {
            $query->whereHas('users', fn($q) => $q->where('users.id', $user->id));
        } elseif ($filter === 'done') {
            $query->whereHas('users', fn($q) => $q
                ->where('users.id', $user->id)
                ->whereNotNull('assignment_user.done_at'));
        } elseif ($filter === 'pending') {
            $query->where(function($q) use ($user) {
                $q->whereDoesntHave('users', fn($qq) => $qq->where('users.id', $user->id))
                  ->orWhereHas('users', fn($qq) => $qq
                      ->where('users.id', $user->id)
                      ->whereNull('assignment_user.done_at'));
            });
        }

        // Sort
        $sort = $req->get('sort', 'due');
        if ($sort === 'newest')       $query->latest();
        elseif ($sort === 'oldest')   $query->oldest();
        else                          $query->orderByRaw('CASE WHEN due_at IS NULL THEN 1 ELSE 0 END, due_at ASC'); // due soonest

        $assignments = $query->paginate(15)->withQueryString();

        // personal completed count for header cue
        $completedCount = $user->completedAssignmentsCountForGroup($group);

        return view('groups.tasks.index', compact('group', 'assignments', 'completedCount', 'filter', 'sort'));
    }

    // app/Http/Controllers/GroupAssignmentController.php

    public function create(StudyGroup $group)
    {
        $this->authorize('create', [Assignment::class, $group]);
        return view('groups.tasks.create', compact('group'));
    }

    public function store(Request $request, StudyGroup $group)
    {
        $this->authorize('create', [Assignment::class, $group]);

        $data = $request->validate([
            'title'       => ['required','string','max:255'],
            'due_at'      => ['nullable','date'],
            'description' => ['nullable','string'],
        ]);

        if (!empty($data['due_at'])) {
            $data['due_at'] = \Illuminate\Support\Carbon::parse($data['due_at']);
        }

        $data['study_group_id'] = $group->id;
        Assignment::create($data);

        return redirect()->route('groups.tasks.index', $group)->with('status', 'Task created.');
    }

    public function edit(StudyGroup $group, Assignment $assignment)
    {
        $this->authorize('update', $assignment);
        abort_unless($assignment->study_group_id === $group->id, 404);

        return view('groups.tasks.edit', compact('group','assignment'));
    }

    public function update(Request $request, StudyGroup $group, Assignment $assignment)
    {
        $this->authorize('update', $assignment);
        abort_unless($assignment->study_group_id === $group->id, 404);

        $data = $request->validate([
            'title'       => ['required','string','max:255'],
            'due_at'      => ['nullable','date'],
            'description' => ['nullable','string'],
        ]);

        if (!empty($data['due_at'])) {
            $data['due_at'] = \Illuminate\Support\Carbon::parse($data['due_at']);
        }

        $assignment->update($data);

        return redirect()->route('groups.tasks.index', $group)->with('status', 'Task updated.');
        }

    public function destroy(StudyGroup $group, Assignment $assignment)
    {
        $this->authorize('delete', $assignment);
        abort_unless($assignment->study_group_id === $group->id, 404);

        $assignment->delete();

        return redirect()->route('groups.tasks.index', $group)->with('status', 'Task deleted.');
    }



    public function toggleDone(StudyGroup $group, Assignment $assignment, Request $req)
    {
    $this->authorize('toggleDone', $assignment);
    abort_unless($assignment->study_group_id === $group->id, 404);

    $user = $req->user();
    $existing = $user->assignments()->where('assignments.id', $assignment->id)->first();

    $now = now(config('app.timezone'));
    $done = false;

    if ($existing && $existing->pivot->done_at) {
        $user->assignments()->updateExistingPivot($assignment->id, ['done_at' => null]);
        $done = false;
    } else {
        $existing
            ? $user->assignments()->updateExistingPivot($assignment->id, ['done_at' => $now])
            : $user->assignments()->attach($assignment->id, ['done_at' => $now]);
        $done = true;
    }

    $completed = $user->completedAssignmentsCountForGroup($group);

    if ($req->expectsJson()) {
        return response()->json([
            'done' => $done,
            'completedCount' => $completed,
        ]);
    }

    return back()->with('status', 'Updated.');
}

}

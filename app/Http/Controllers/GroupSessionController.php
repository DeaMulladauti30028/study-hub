<?php

namespace App\Http\Controllers;

use App\Models\GroupSession;
use App\Models\StudyGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class GroupSessionController extends Controller
{
    public function index(StudyGroup $group)
    {
        $sessions = $group->sessions()->orderBy('starts_at', 'asc')->paginate(10);
        return view('sessions.index', compact('group', 'sessions'));
    }

    public function create(StudyGroup $group)
    {
        return view('sessions.create', compact('group'));
    }

    public function store(Request $request, StudyGroup $group)
    {
        $data = $request->validate([
            'starts_at'        => ['required','date'],
            'duration_minutes' => ['required','integer','min:15','max:480'],
            'video_url'        => ['nullable','url','max:255'],
            'notes'            => ['nullable','string'],
        ]);

        $data['starts_at'] = Carbon::parse($data['starts_at']);
        $data['study_group_id'] = $group->id;

        GroupSession::create($data);

        return redirect()->route('groups.sessions.index', $group)
            ->with('status', 'Session created!');
    }
}

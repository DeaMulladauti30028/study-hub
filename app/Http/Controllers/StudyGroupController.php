<?php

namespace App\Http\Controllers;

use App\Models\StudyGroup;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudyGroupController extends Controller
{
    
    protected $fillable = ['course_id','name','description','owner_id'];

    public function index(Request $request){
    $now  = now();
    $soon = now()->addDays(3);        
    $onlyUpcoming = $request->boolean('upcoming');
    $search = trim((string) $request->get('q', ''));
    $sort   = $request->get('sort', 'new'); 

    $builder = StudyGroup::query()
    ->with(['course', 'nextSession'])
    ->withCount('members') // ← add this
    ->withCount([
        'assignments as overdue_assignments_count' => fn($q) =>
            $q->whereNotNull('due_at')->where('due_at', '<', $now),
        'assignments as due_soon_assignments_count' => fn($q) =>
            $q->whereNotNull('due_at')->whereBetween('due_at', [$now, $soon]),
    ])
    ->when($onlyUpcoming, fn($q) =>
        $q->whereHas('sessions', fn($qq) => $qq->where('starts_at', '>=', now()))
    )
    ->when($search !== '', function ($q) use ($search) {
        $q->where(function ($qq) use ($search) {
            $qq->where('name', 'like', "%{$search}%")
               ->orWhereHas('course', function ($cq) use ($search) {
                   $cq->where('title', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
               });
        });
    })
    ->select('study_groups.*');


    if ($sort === 'old') {
        $builder->orderBy('created_at', 'asc');
    } elseif ($sort === 'members') {
        $builder->orderBy('members_count', 'desc')->orderBy('created_at', 'desc');
    } elseif ($sort === 'soonest') {
      
        $nextSessions = DB::table('group_sessions')
            ->selectRaw('study_group_id, MIN(starts_at) as next_starts_at')
            ->where('starts_at', '>=', now())
            ->groupBy('study_group_id');

        $builder
            ->leftJoinSub($nextSessions, 'ns', function ($join) {
                $join->on('ns.study_group_id', '=', 'study_groups.id');
            })
            ->orderByRaw('ns.next_starts_at IS NULL') 
            ->orderBy('ns.next_starts_at', 'asc')
            ->orderBy('study_groups.created_at', 'desc');
    } else { 
        $builder->orderBy('created_at', 'desc');
    }

    $groups = $builder->paginate(10)->withQueryString();

    $myGroupIds = auth()->user()->studyGroups()->pluck('study_groups.id')->toArray();

    return view('groups.index', compact('groups', 'myGroupIds', 'onlyUpcoming', 'search', 'sort'));
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
        $validated = $request->validate([
            'course_id'   => ['required', 'exists:courses,id'],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $group = StudyGroup::create([
            'course_id'   => $validated['course_id'],
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'owner_id'    => $request->user()->id,
        ]);

        $group->members()->syncWithoutDetaching([$request->user()->id]);

        return redirect()
            ->route('groups.index')
            ->with('status', 'Study group created!');
    }
    // app/Http/Controllers/StudyGroupController.php

    public function show(StudyGroup $group)
    {
        $group->load([
            'course',
            'members' => fn ($q) => $q->select('users.id','users.name')->withPivot('is_moderator'),
            'nextSession',
        ]);

        return view('groups.show', compact('group'));
    }



}

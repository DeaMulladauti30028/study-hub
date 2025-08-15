<?php


namespace App\Http\Controllers;

use App\Http\Requests\StoreContributionRequest;
use App\Models\Contribution;
use App\Models\StudyGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateContributionRequest;

class ContributionController extends Controller
{
            // GET /groups/{group}/contributions
            public function index(StudyGroup $group)
            {
                $this->authorize('create', [Contribution::class, $group]); // membership gate

        $filter = request('filter'); // 'accepted' | 'mine' | null
        $sort   = request('sort');   // 'helpful' | 'newest' | 'oldest'

        $query = Contribution::with('user')
            ->withCount('helpfuls')
            ->withCount('comments')
            ->where('study_group_id', $group->id);

        // Filters
        if ($filter === 'accepted') {
            $query->where('is_accepted', true);
        } elseif ($filter === 'mine') {
            $query->where('user_id', auth()->id());
        }

        // Sorting
        if ($sort === 'helpful') {
            $query->orderByDesc('helpfuls_count')->orderByDesc('created_at');
        } elseif ($sort === 'oldest') {
            $query->orderBy('created_at');
        } else { // default: newest
            $query->orderByDesc('created_at');
        }

        $items = $query->paginate(12)->appends(request()->query());

        return view('contributions.index', compact('group', 'items'));

    }

    // GET /groups/{group}/contributions/create
    public function create(StudyGroup $group)
    {
        $this->authorize('create', [Contribution::class, $group]);
        return view('contributions.create', compact('group'));
    }

    // POST /groups/{group}/contributions
    public function store(StoreContributionRequest $request, StudyGroup $group)
    {
        $this->authorize('create', [Contribution::class, $group]);

        $path = null;
        $mime = null;
        if ($request->hasFile('file')) {
            // store privately
            $path = $request->file('file')->store('contributions', 'private');
            $mime = $request->file('file')->getMimeType();
        }

        $contribution = Contribution::create([
            'study_group_id' => $group->id,
            'user_id'        => $request->user()->id,
            'title'          => $request->string('title'),
            'content'        => $request->input('content'),
            'file_path'      => $path,
            'mime_type'      => $mime,
        ]);

        return redirect()
            ->route('groups.contributions.show', [$group, $contribution])
            ->with('status', 'Contribution posted!');
    }

    // GET /groups/{group}/contributions/{contribution}
    public function show(StudyGroup $group, Contribution $contribution)
    {
        abort_unless($contribution->study_group_id === $group->id, 404);
        $this->authorize('view', $contribution);
        

        $contribution->load('user');

        $hasHelpful = $contribution->helpfuls()->whereKey(auth()->id())->exists();
        return view('contributions.show', compact('group', 'contribution', 'hasHelpful'));

    }

    // GET /groups/{group}/contributions/{contribution}/file
    public function file(StudyGroup $group, Contribution $contribution)
    {
        abort_unless($contribution->study_group_id === $group->id, 404);
        $this->authorize('view', $contribution);

        abort_unless($contribution->file_path, 404);

        $absPath = Storage::disk('private')->path($contribution->file_path);
        $filename = basename($absPath);

        // Inline preview for images/PDF; download for others
        $inlineable = $contribution->mime_type && (
            str_starts_with($contribution->mime_type, 'image/')
            || $contribution->mime_type === 'application/pdf'
        );

        if ($inlineable) {
            return response()->file($absPath, [
                'Content-Type'        => $contribution->mime_type ?? 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="'.$filename.'"',
            ]);
        }

        return response()->download($absPath, $filename);
    }

    // GET /groups/{group}/contributions/{contribution}/edit
        public function edit(StudyGroup $group, Contribution $contribution)
        {
            abort_unless($contribution->study_group_id === $group->id, 404);
            $this->authorize('update', $contribution);

            return view('contributions.edit', compact('group', 'contribution'));
        }

        // PUT /groups/{group}/contributions/{contribution}
        public function update(UpdateContributionRequest $request, StudyGroup $group, Contribution $contribution)
        {
            abort_unless($contribution->study_group_id === $group->id, 404);
            $this->authorize('update', $contribution);

            $wasEdited = false;

            // Title/content changes
            $newTitle   = (string) $request->string('title');
            $newContent = $request->input('content');

            if ($newTitle !== $contribution->title || $newContent !== $contribution->content) {
                $wasEdited = true;
            }

            // File replacement (optional)
            if ($request->hasFile('file')) {
                // delete old file if existed
                if ($contribution->file_path) {
                    \Storage::disk('private')->delete($contribution->file_path);
                }
                $path = $request->file('file')->store('contributions', 'private');
                $mime = $request->file('file')->getMimeType();

                $contribution->file_path = $path;
                $contribution->mime_type = $mime;
                $wasEdited = true;
            } elseif ($request->filled('content') && !$request->hasFile('file')) {
                // keep existing file as-is (no change)
            }

            $contribution->title     = $newTitle;
            $contribution->content   = $newContent;
            if ($wasEdited) {
                $contribution->is_edited = true;
            }
            $contribution->save();

            return redirect()
                ->route('groups.contributions.show', [$group, $contribution])
                ->with('status', 'Contribution updated.');
        }

        // DELETE /groups/{group}/contributions/{contribution}
        public function destroy(StudyGroup $group, Contribution $contribution)
        {
            abort_unless($contribution->study_group_id === $group->id, 404);
            $this->authorize('delete', $contribution);

            if ($contribution->file_path) {
                \Storage::disk('private')->delete($contribution->file_path);
            }
            $contribution->delete();

            return redirect()
                ->route('groups.contributions.index', $group)
                ->with('status', 'Contribution removed.');
        }

        public function toggleHelpful(StudyGroup $group, Contribution $contribution)
        {
            abort_unless($contribution->study_group_id === $group->id, 404);
            // Membership check (reuse view permission)
            $this->authorize('view', $contribution);

            // Optional fairness rule: author cannot upvote own post
            if (auth()->id() === $contribution->user_id) {
                abort(403, 'Authors cannot mark their own contribution as helpful.');
            }

            $already = $contribution->helpfuls()->whereKey(auth()->id())->exists();

            if ($already) {
                $contribution->helpfuls()->detach(auth()->id());
                $msg = 'Removed your helpful mark.';
            } else {
                $contribution->helpfuls()->attach(auth()->id());
                $msg = 'Marked as helpful.';
            }

            return back()->with('status', $msg);
        }

        public function toggleEndorse(StudyGroup $group, Contribution $contribution)
        {
            abort_unless($contribution->study_group_id === $group->id, 404);
            $this->authorize('endorse', $contribution);

            // Optional: prevent author from endorsing their own post even if they’re owner
            if (auth()->id() === $contribution->user_id) {
                abort(403, 'Authors cannot endorse their own contribution.');
            }

            if ($contribution->is_accepted) {
                // un-endorse
                $contribution->is_accepted = false;
                $contribution->accepted_by = null;
                $contribution->accepted_at = null;
                $msg = 'Endorsement removed.';
            } else {
                // endorse
                $contribution->is_accepted = true;
                $contribution->accepted_by = auth()->id();
                $contribution->accepted_at = now();
                $msg = 'Contribution endorsed.';
            }

            $contribution->save();

            return back()->with('status', $msg);
        }



        
}

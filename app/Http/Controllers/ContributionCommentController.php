<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudyGroup;
use App\Models\Contribution;
use App\Models\ContributionComment;


class ContributionCommentController extends Controller
{
    public function store(Request $request, StudyGroup $group, Contribution $contribution)
    {
        abort_unless($contribution->study_group_id === $group->id, 404);
        $this->authorize('create', [ContributionComment::class, $contribution]);

        $data = $request->validate([
            'body' => ['required','string','max:2000'],
        ]);

        ContributionComment::create([
            'contribution_id' => $contribution->id,
            'user_id'         => $request->user()->id,
            'body'            => $data['body'],
        ]);

        return back()->with('status', 'Comment added.');
    }

    public function destroy(StudyGroup $group, Contribution $contribution, ContributionComment $comment)
    {
        abort_unless($contribution->study_group_id === $group->id, 404);
        abort_unless($comment->contribution_id === $contribution->id, 404);

        $this->authorize('delete', $comment);

        $comment->delete();

        return back()->with('status', 'Comment removed.');
    }
}

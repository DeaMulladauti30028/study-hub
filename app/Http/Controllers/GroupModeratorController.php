<?php
namespace App\Http\Controllers;

use App\Models\StudyGroup;
use App\Models\User;

class GroupModeratorController extends Controller
{
    public function promote(StudyGroup $group, User $user)
    {
        $this->authorize('manageModerators', $group);

        abort_unless($group->members()->whereKey($user->id)->exists(), 404);

        $group->members()->updateExistingPivot($user->id, ['is_moderator' => true]);

        return back()->with('status', 'User promoted to moderator.');
    }

    public function demote(StudyGroup $group, User $user)
    {
        $this->authorize('manageModerators', $group);

        abort_if((int)$group->owner_id === (int)$user->id, 403);

        $group->members()->updateExistingPivot($user->id, ['is_moderator' => false]);

        return back()->with('status', 'Moderator removed.');
    }
}

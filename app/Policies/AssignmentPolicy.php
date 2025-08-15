<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\StudyGroup;
use App\Models\User;

class AssignmentPolicy
{
    public function viewAny(User $user, StudyGroup $group): bool
    {
        return $group->isMember($user);
    }

    public function view(User $user, Assignment $assignment): bool
    {
        return $assignment->group->isMember($user);
    }

    public function create(User $user, StudyGroup $group): bool
    {
        return $group->isOwnerOrModerator($user); // currently == isMember()
    }

    public function update(User $user, Assignment $assignment): bool
    {
        return $assignment->group->isOwnerOrModerator($user);
    }

    public function delete(User $user, Assignment $assignment): bool
    {
        return $assignment->group->isOwnerOrModerator($user);
    }

    public function toggleDone(User $user, Assignment $assignment): bool
    {
        return $assignment->group->isMember($user);
    }
}

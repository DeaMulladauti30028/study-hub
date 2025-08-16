<?php

namespace App\Policies;

use App\Models\StudyGroup;
use App\Models\User;

class StudyGroupPolicy
{
    /**
     * Only the group owner can manage moderators.
     */
    public function manageModerators(User $user, StudyGroup $group): bool
    {
        return $group->isOwner($user);
    }


}

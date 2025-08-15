<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StudyGroup;
use App\Models\Contribution;

class ContributionPolicy
{
    // Helper: is the user a member (or owner) of the group?
    protected function isMember(User $user, StudyGroup $group): bool
    {
        // Treat owner as a member
        $isOwner = (int) $group->owner_id === (int) $user->id;
    
        return $isOwner || $group->members()->whereKey($user->id)->exists();
    }

    // View a specific contribution → must be in its group
    public function view(User $user, Contribution $contribution): bool
    {
        return $this->isMember($user, $contribution->group);
    }

    // Create a contribution inside a given group
    // Call with: $this->authorize('create', [Contribution::class, $group]);
    public function create(User $user, StudyGroup $group): bool
    {
        return $this->isMember($user, $group);
    }

    // Update/Delete → author only, and still a member
    public function update(User $user, Contribution $contribution): bool
    {
        return $this->isMember($user, $contribution->group)
            && $contribution->user_id === $user->id;
    }

    public function delete(User $user, Contribution $contribution): bool
    {
        return $this->update($user, $contribution);
    }


    public function endorse(User $user, Contribution $contribution): bool
    {
        $group = $contribution->group;
    
        // Must be in the group (owner counts as member)
        if (!$this->isMember($user, $group)) {
            return false;
        }
    
        // Only the owner can endorse (add moderators later if needed)
        return (int) $group->owner_id === (int) $user->id;
    }
}

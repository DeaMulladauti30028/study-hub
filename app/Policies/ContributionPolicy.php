<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StudyGroup;
use App\Models\Contribution;

class ContributionPolicy
{
    /**
     * Member check (owner counts as member).
     */
    protected function isMember(User $user, StudyGroup $group): bool
    {
        $isOwner = (int) $group->owner_id === (int) $user->id;

        // If you added pivot with is_moderator, the membership relation still applies.
        return $isOwner || $group->members()->whereKey($user->id)->exists();
    }

    /**
     * Staff = Owner OR Moderator.
     * Uses StudyGroup::isStaff($user) if available; falls back to owner-only.
     */
    protected function isStaff(User $user, StudyGroup $group): bool
    {
        if (method_exists($group, 'isStaff')) {
            return $group->isStaff($user);
        }
        return (int) $group->owner_id === (int) $user->id;
    }

    /**
     * View a specific contribution → must be in its group.
     */
    public function view(User $user, Contribution $contribution): bool
    {
        return $this->isMember($user, $contribution->group);
    }

    /**
     * Create inside a given group.
     * Call with: $this->authorize('create', [Contribution::class, $group]);
     */
    public function create(User $user, StudyGroup $group): bool
    {
        return $this->isMember($user, $group);
    }

    /**
     * Update → author only (still must be a member).
     */
    public function update(User $user, Contribution $contribution): bool
    {
        return $this->isMember($user, $contribution->group)
            && (int) $contribution->user_id === (int) $user->id;
    }

    /**
     * Delete → author OR staff (owner/moderator) can delete.
     * This grants moderators real moderation power.
     */
    public function delete(User $user, Contribution $contribution): bool
    {
        return (int) $contribution->user_id === (int) $user->id
            || $this->isStaff($user, $contribution->group);
    }

    /**
     * Endorse → staff (owner OR moderator).
     */
    public function endorse(User $user, Contribution $contribution): bool
    {
        return $this->isStaff($user, $contribution->group);
    }
}

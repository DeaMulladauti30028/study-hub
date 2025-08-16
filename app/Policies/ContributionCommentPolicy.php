<?php

namespace App\Policies;

use App\Models\{Contribution, ContributionComment, User};

class ContributionCommentPolicy
{
    /**
     * Member check (owner counts as member)
     */
    protected function isMember(User $user, $group): bool
    {
        $isOwner = (int) $group->owner_id === (int) $user->id;
        return $isOwner || $group->members()->whereKey($user->id)->exists();
    }

    /**
     * Staff = owner OR moderator
     * Uses StudyGroup::isStaff if available; falls back to owner-only.
     */
    protected function isStaff(User $user, $group): bool
    {
        if (method_exists($group, 'isStaff')) {
            return $group->isStaff($user);
        }
        return (int) $group->owner_id === (int) $user->id;
    }

    /**
     * Members of the contribution's group can create
     */
    public function create(User $user, Contribution $contribution): bool
    {
        $group = $contribution->group;
        return $this->isMember($user, $group);
    }

    /**
     * Delete if author OR staff (owner/moderator)
     */
    public function delete(User $user, ContributionComment $comment): bool
    {
        $group = $comment->contribution->group;

        return (int) $comment->user_id === (int) $user->id
            || $this->isStaff($user, $group);
    }

    // (Optional) Allow viewing only to members
    public function view(User $user, ContributionComment $comment): bool
    {
        return $this->isMember($user, $comment->contribution->group);
    }

    // (Optional) Author can edit own comment; staff can also edit (moderation)
    public function update(User $user, ContributionComment $comment): bool
    {
        return (int) $comment->user_id === (int) $user->id
            || $this->isStaff($user, $comment->contribution->group);
    }
}

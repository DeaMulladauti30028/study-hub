<?php

namespace App\Policies;

use App\Models\{GroupMaterial, StudyGroup, User};

class GroupMaterialPolicy
{
    /**
     * Member check (owner counts as member)
     */
    protected function isMember(User $user, StudyGroup $group): bool
    {
        $isOwner = (int) $group->owner_id === (int) $user->id;

        return $isOwner || $group->members()->where('users.id', $user->id)->exists();
    }

    /**
     * Staff = owner OR moderator
     * Uses StudyGroup::isStaff if available; falls back to owner-only.
     */
    protected function isStaff(User $user, StudyGroup $group): bool
    {
        if (method_exists($group, 'isStaff')) {
            return $group->isStaff($user);
        }
        return (int) $group->owner_id === (int) $user->id;
    }

    // List materials in a group
    public function index(User $user, StudyGroup $group): bool
    {
        return $this->isMember($user, $group);
    }

    // Optional: Laravel naming for listing
    // public function viewAny(User $user, StudyGroup $group): bool
    // {
    //     return $this->isMember($user, $group);
    // }

    // Upload
    public function create(User $user, StudyGroup $group): bool
    {
        return $this->isMember($user, $group);
    }

    // VIEW (used by inline preview)
    public function view(User $user, GroupMaterial $material): bool
    {
        return $this->isMember($user, $material->group);
    }

    // Download
    public function download(User $user, GroupMaterial $material): bool
    {
        return $this->isMember($user, $material->group);
    }

    /**
     * Delete:
     * - author can delete own
     * - staff (owner/moderator) can delete any (moderation)
     */
    public function delete(User $user, GroupMaterial $material): bool
    {
        return ((int) $material->user_id === (int) $user->id)
            || $this->isStaff($user, $material->group);
    }

    /**
     * Optional: Pin/Unpin (use if you add a `pinned_at` or `is_pinned` column)
     */
    public function pin(User $user, GroupMaterial $material): bool {
        return method_exists($material->group, 'isStaff')
            ? $material->group->isStaff($user)
            : (int)$material->group->owner_id === (int)$user->id;
    }
    
    public function unpin(User $user, GroupMaterial $material): bool {
        return $this->pin($user, $material);
    }
    
}

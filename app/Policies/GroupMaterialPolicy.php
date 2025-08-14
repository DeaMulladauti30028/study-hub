<?php

namespace App\Policies;

use App\Models\{GroupMaterial, StudyGroup, User};

class GroupMaterialPolicy
{
    protected function isMember(User $user, StudyGroup $group): bool {
        return $group->members()->where('users.id', $user->id)->exists();
    }

    // List materials in a group
    public function index(User $user, StudyGroup $group): bool {
        return $this->isMember($user, $group);
    }

    // Optional: if you prefer Laravel naming for listing:
    // public function viewAny(User $user, StudyGroup $group): bool {
    //     return $this->isMember($user, $group);
    // }

    // Upload
    public function create(User $user, StudyGroup $group): bool {
        return $this->isMember($user, $group);
    }

    // VIEW (used by inline preview)
    public function view(User $user, GroupMaterial $material): bool {
        return $this->isMember($user, $material->group);
    }

    // Download
    public function download(User $user, GroupMaterial $material): bool {
        return $this->isMember($user, $material->group);
    }

    // Delete (uploader or whatever rule you want)
    public function delete(User $user, GroupMaterial $material): bool {
        return $this->isMember($user, $material->group)
            && $material->user_id === $user->id;
    }
}


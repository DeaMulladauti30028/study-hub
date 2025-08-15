<?php

namespace App\Policies;

use App\Models\Contribution;
use App\Models\ContributionComment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ContributionCommentPolicy
{
     // Members of the contribution's group can create
     public function create(User $user, Contribution $contribution): bool
     {
         $group = $contribution->group;
         $isOwner = (int)$group->owner_id === (int)$user->id;
         return $isOwner || $group->members()->whereKey($user->id)->exists();
     }
 
     // Delete if author OR group owner (simple moderation)
     public function delete(User $user, ContributionComment $comment): bool
     {
         $group = $comment->contribution->group;
         $isOwner = (int)$group->owner_id === (int)$user->id;
         return $isOwner || (int)$comment->user_id === (int)$user->id;
     }
}

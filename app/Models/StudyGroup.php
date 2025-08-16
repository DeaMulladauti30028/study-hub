<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyGroup extends Model
{
    use HasFactory;

    // Add 'owner_id' if you set it via mass assignment (e.g., on create)
    protected $fillable = ['course_id', 'name', 'description', 'owner_id'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Members pivot now explicitly uses 'study_group_user' and carries 'is_moderator'
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'study_group_user')
            ->withPivot('is_moderator')
            ->withTimestamps();
    }

    /**
     * Convenience relation for moderators only
     */
    public function moderators()
    {
        return $this->belongsToMany(User::class, 'study_group_user')
            ->withPivot('is_moderator')
            ->wherePivot('is_moderator', true);
    }

    public function sessions()
    {
        return $this->hasMany(GroupSession::class); 
    }

    public function nextSession()
    {
        return $this->hasOne(GroupSession::class)
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at', 'asc');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // ---------- Helpers ----------

    public function isMember(User $user): bool
    {
        return $this->members()->whereKey($user->id)->exists();
    }

    public function isOwner(User $user): bool
    {
        return (int) $this->owner_id === (int) $user->id;
    }

    public function isModerator(User $user): bool
    {
        return $this->members()
            ->where('users.id', $user->id)
            ->wherePivot('is_moderator', true)
            ->exists();
    }

    public function isStaff(User $user): bool
    {
        return $this->isOwner($user) || $this->isModerator($user);
    }

    /**
     * Back-compat alias used elsewhere in your code
     */
    public function isOwnerOrModerator(User $user): bool
    {
        return $this->isStaff($user);
    }
}

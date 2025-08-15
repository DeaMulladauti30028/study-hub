<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyGroup extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'name', 'description'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
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

    public function isMember(User $user): bool
    {
    return $this->members()->whereKey($user->id)->exists();
    }
    
    /**
 * TODO: fix when i add roles/owner.
 */
    public function isOwnerOrModerator(User $user): bool
    {
    return $this->isMember($user);
    }




}

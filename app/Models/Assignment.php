<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    // If not already present:
    protected $fillable = ['title','description','due_at','study_group_id'];
    protected $casts = ['due_at' => 'datetime'];

    public function group()
    {
        return $this->belongsTo(StudyGroup::class, 'study_group_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('done_at')
            ->withTimestamps();
    }
}
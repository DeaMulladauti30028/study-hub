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
    return $this->hasMany(\App\Models\GroupSession::class); 
    }

    public function nextSession()
    {
    return $this->hasOne(\App\Models\GroupSession::class)
        ->where('starts_at', '>=', now())
        ->orderBy('starts_at', 'asc');
    }

    public function assignments()
    {
    return $this->hasMany(Assignment::class);
    }



}

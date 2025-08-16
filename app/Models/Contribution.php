<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contribution extends Model
{
    use HasFactory;

    protected $casts = [
        'accepted_at' => 'datetime',
        'is_accepted' => 'boolean',
        
    ];
    

    protected $fillable = [
        'study_group_id',
        'user_id',
        'title',
        'content',
        'file_path',
        'mime_type',
        'is_edited',
    ];

    
    public function acceptedBy()
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }
    

    public function group()
    {
        return $this->belongsTo(StudyGroup::class, 'study_group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function helpfuls()
    {
        return $this->belongsToMany(User::class, 'contribution_helpfuls')->withTimestamps();
    }

    public function comments()
{
    return $this->hasMany(ContributionComment::class)->latest();
}


}

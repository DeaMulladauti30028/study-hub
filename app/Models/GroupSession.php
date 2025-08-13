<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_group_id',
        'starts_at',
        'duration_minutes',
        'video_url',
        'notes',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
    ];

    public function studyGroup()
    {
        return $this->belongsTo(\App\Models\StudyGroup::class);
    }

    

    
}

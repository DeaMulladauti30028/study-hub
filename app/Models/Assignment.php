<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = ['study_group_id','title','due_at','description'];

    protected $casts = [
        'due_at' => 'datetime',
    ];

    public function studyGroup()
    {
        return $this->belongsTo(StudyGroup::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_group_id',
        'user_id',
        'title',
        'content',
        'file_path',
        'mime_type',
        'is_edited',
    ];

    public function group()
    {
        return $this->belongsTo(StudyGroup::class, 'study_group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

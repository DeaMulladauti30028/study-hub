<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyGroup extends Model
{
    protected $fillable = ['course_id', 'name', 'description'];

public function course()
{
    return $this->belongsTo(\App\Models\Course::class);
}

}

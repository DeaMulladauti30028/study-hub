<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContributionComment extends Model
{
    use HasFactory;

    protected $fillable = ['contribution_id','user_id','body'];

    public function contribution() { return $this->belongsTo(Contribution::class); }
    public function user() { return $this->belongsTo(User::class); }
}

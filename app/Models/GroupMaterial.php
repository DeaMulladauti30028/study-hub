<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupMaterial extends Model
{
    protected $casts = [
        'pinned_at' => 'datetime',
    ];
    
    protected $fillable = [
        'study_group_id','user_id','title','file_path','file_size','mime_type','original_name'
    ];

    public function group(): BelongsTo {
        return $this->belongsTo(StudyGroup::class, 'study_group_id');
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /** Search by title (case-insensitive LIKE) */
    public function scopeSearchTitle($q, ?string $term)
{
    if (!filled($term)) return $q;
    return $q->where('title', 'like', '%'.str_replace('%','\%',$term).'%');
}

public function scopeOfBasicType($q, ?string $type)
{
    if (!filled($type)) return $q;
    return match ($type) {
        'image' => $q->where('mime_type', 'like', 'image/%'),
        'video' => $q->where('mime_type', 'like', 'video/%'),
        'pdf'   => $q->where('mime_type', 'application/pdf'),
        'slides'=> $q->where(fn($qq)=>$qq->where('mime_type','like','%presentation%')->orWhere('mime_type','like','%powerpoint%')),
        'sheets'=> $q->where('mime_type','like','%spreadsheet%'),
        'docs'  => $q->where(fn($qq)=>$qq->where('mime_type','like','%msword%')->orWhere('mime_type','like','%word%')->orWhere('mime_type','application/rtf')->orWhere('mime_type','application/vnd.oasis.opendocument.text')),
        default => $q
    };
}

    /** Optional: simple derived category string for the UI */
    public function getCategoryAttribute(): string
    {
        $m = $this->mime_type ?? '';
        return str_starts_with($m,'image/')   ? 'IMAGE'  :
               (str_starts_with($m,'video/')  ? 'VIDEO'  :
               ($m === 'application/pdf'      ? 'PDF'    :
               (str_contains($m,'presentation') ? 'SLIDES' :
               (str_contains($m,'spreadsheet')  ? 'SHEETS' :
               ((str_contains($m,'msword') || str_contains($m,'word')) ? 'DOCS' : 'FILE')))));
    }
}


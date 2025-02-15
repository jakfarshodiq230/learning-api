<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'title',
        'file_path',
    ];

    // Relasi dengan Course (Materi milik satu mata kuliah)
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}

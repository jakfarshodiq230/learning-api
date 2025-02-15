<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'deadline',
    ];

    // Relasi dengan Course (Tugas milik satu mata kuliah)
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Relasi dengan Submission (Tugas memiliki banyak jawaban)
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}

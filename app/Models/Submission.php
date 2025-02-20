<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Submission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'assignment_id',
        'student_id',
        'file_path',
        'score',
    ];

    // Relasi dengan Assignment (Jawaban milik satu tugas)
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    // Relasi dengan User (Jawaban milik satu mahasiswa)
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}

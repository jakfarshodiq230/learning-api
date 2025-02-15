<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'lecturer_id',
    ];

    // Relasi dengan User (Dosen yang mengajar mata kuliah)
    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    // Relasi dengan User (Mahasiswa yang mengikuti mata kuliah)
    public function students()
    {
        return $this->belongsToMany(User::class, 'course_student', 'course_id', 'student_id');
    }

    // Relasi dengan Material (Mata kuliah memiliki banyak materi)
    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    // Relasi dengan Assignment (Mata kuliah memiliki banyak tugas)
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    // Relasi dengan Discussion (Mata kuliah memiliki banyak diskusi)
    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }
}

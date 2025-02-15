<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discussion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'user_id',
        'content',
    ];

    // Relasi dengan Course (Diskusi milik satu mata kuliah)
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Relasi dengan User (Diskusi dibuat oleh satu pengguna)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan Reply (Diskusi memiliki banyak balasan)
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }
}

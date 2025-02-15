<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reply extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'discussion_id',
        'user_id',
        'content',
    ];

    // Relasi dengan Discussion (Balasan milik satu diskusi)
    public function discussion()
    {
        return $this->belongsTo(Discussion::class);
    }

    // Relasi dengan User (Balasan dibuat oleh satu pengguna)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

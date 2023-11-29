<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tutorial extends Model
{
    use HasFactory;
    protected $table = 'tutorials';
    protected $fillable = [
        'user_id',
        'judul_tutorial',
        'deskripsi',
        'bahan',
        'alat',
        'langkah_tutorial',
        'foto',
    ];

    public function user(){
        /**
         * Belong to User
         *
         * @return Collection
         *
         **/
        return $this->belongsTo(User::class);
    }
}

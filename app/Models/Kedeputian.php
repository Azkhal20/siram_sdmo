<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kedeputian extends Model
{
    protected $fillable = ['nama'];

    // Relasi ke peserta magang (one-to-many)
    public function pesertaMagang(): HasMany
    {
        return $this->hasMany(PesertaMagang::class, 'kedeputian_id');
    }
}
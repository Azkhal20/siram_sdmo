<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PesertaMagang extends Model
{
    protected $table = 'peserta_magang';
    protected $fillable = ['kedeputian_id', 'nama', 'nomor_induk'];

    public function kedeputian(): BelongsTo
    {
        return $this->belongsTo(Kedeputian::class);
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }
}

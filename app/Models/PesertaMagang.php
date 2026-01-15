<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesertaMagang extends Model
{
    protected $table = 'peserta_magang';

    protected $fillable = [
        'nama',
        'nomor_induk',
        'kedeputian_id',
        'unit_kerja_text' // ✅ DITAMBAHKAN
    ];

    public function absensis(): HasMany // ✅ Plural untuk konsistensi
    {
        return $this->hasMany(Absensi::class, 'peserta_magang_id');
    }

    public function kedeputian(): BelongsTo
    {
        return $this->belongsTo(Kedeputian::class, 'kedeputian_id');
    }
}
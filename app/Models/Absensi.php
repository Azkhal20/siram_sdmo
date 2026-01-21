<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    protected $fillable = [
        'peserta_magang_id',
        'tanggal',
        'kehadiran',
        'jam_masuk',
        'jam_pulang',
        'menit_telat',
        'menit_pulang_cepat',
        'keterangan', 
    ];

    protected $casts = [
        'tanggal' => 'date',
        'menit_telat' => 'integer',
        'menit_pulang_cepat' => 'integer',
        'jam_masuk' => 'datetime:H:i:s',
        'jam_pulang' => 'datetime:H:i:s',
    ];

    public function pesertaMagang(): BelongsTo
    {
        return $this->belongsTo(PesertaMagang::class, 'peserta_magang_id');
    }
}
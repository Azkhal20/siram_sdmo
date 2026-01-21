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


    // Helper to calculate difference in seconds between two times
    private function calculateDiffSeconds($startTime, $endTime)
    {
        if (!$startTime || !$endTime) return 0;
        $start = \Carbon\Carbon::parse($startTime);
        $end = \Carbon\Carbon::parse($endTime);
        return $end->diffInSeconds($start); // returns absolute difference usually, but we need direction
    }

    public function getTelatMasukAttribute()
    {
        if (!$this->jam_masuk) return '-';

        // Parse to get H:i:s and normalize to a clean comparison
        $timeStr = \Carbon\Carbon::parse($this->jam_masuk)->format('H:i:s');
        [$h, $m, $s] = explode(':', $timeStr);
        $totalSeconds = ($h * 3600) + ($m * 60) + $s;
        $limitSeconds = 8 * 3600; // 08:00:00

        if ($totalSeconds > $limitSeconds) {
            $diff = $totalSeconds - $limitSeconds;
            return sprintf('%02d:%02d', intdiv($diff, 3600), intdiv($diff % 3600, 60));
        }

        return '-';
    }

    public function getPulangCepatAttribute()
    {
        if (!$this->jam_pulang) return '-';

        $timeStr = \Carbon\Carbon::parse($this->jam_pulang)->format('H:i:s');
        [$h, $m, $s] = explode(':', $timeStr);
        $totalSeconds = ($h * 3600) + ($m * 60) + $s;
        $limitSeconds = (16 * 3600) + (30 * 60); // 16:30:00

        if ($totalSeconds < $limitSeconds) {
            $diff = $limitSeconds - $totalSeconds;
            return sprintf('%02d:%02d', intdiv($diff, 3600), intdiv($diff % 3600, 60));
        }

        return '-';
    }

    public static function getKeteranganByCode($kehadiran)
    {
        $descMap = [
            'TK'        => 'Tanpa keterangan.',
            'TMDHM'     => 'Tidak absen masuk.',
            'TMDHP'     => 'Tidak absen pulang.',
            'PC1-TMDHM' => 'Pulang cepat < 30 menit & tidak absen masuk.',
            'TM1'       => 'Terlambat masuk < 30 menit.',
            'TM2'       => 'Terlambat masuk > 30 menit.',
            'TM3'       => 'Terlambat masuk > 1 jam.',
            'TM'        => 'Terlambat',
            'PC'        => 'Pulang cepat',
            'PC1'       => 'Pulang cepat < 30 menit.',
            'PC2'       => 'Pulang cepat > 30 menit.',
            'PC3'       => 'Pulang cepat > 1 jam.',
            'HN'        => 'Hadir normal',
            'LJ'        => 'Libur weekend',
            'LN'        => 'Libur nasional.',
        ];

        if (!$kehadiran) return '-';

        // Normalize: Remove spaces, dashes, dots. Convert to upper.
        $cleanCode = strtoupper(str_replace([' ', '-', '.'], '', $kehadiran));

        // Match exact first
        if (isset($descMap[$cleanCode])) {
            return $descMap[$cleanCode];
        }

        // Split into logical components (Longest matches first to prioritize TMDHM over TM)
        // Note: PC1-TMDHM usually normalized to PC1TMDHM
        preg_match_all('/TMDHM|TMDHP|PC\d+|TM\d+|TK|HN|LN|LJ|TM|PC/i', $cleanCode, $matches);

        $finalDescs = [];
        if (!empty($matches[0])) {
            foreach ($matches[0] as $part) {
                $p = strtoupper($part);
                if (isset($descMap[$p])) {
                    $finalDescs[] = $descMap[$p];
                }
            }
        }

        if (empty($finalDescs)) {
            return $kehadiran;
        }

        return implode(' dan ', array_unique($finalDescs));
    }

    public function getKeteranganDetailAttribute()
    {
        return self::getKeteranganByCode($this->kehadiran);
    }
}

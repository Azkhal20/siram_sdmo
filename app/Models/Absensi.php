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
        'keterangan', // Added to fillable
    ];

    protected $casts = [
        'tanggal' => 'date',
        'menit_telat' => 'integer',
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

    public function getKeteranganDetailAttribute()
    {
        $tmCodeCalc = null;
        $pcCodeCalc = null;

        // 1. Calculate Codes again based on detailed logic (same as DataAbsensi)
        if ($this->jam_masuk) {
            $masuk = \Carbon\Carbon::parse($this->jam_masuk);
            $batasMasuk = \Carbon\Carbon::parse($this->jam_masuk)->setTime(8, 0, 0);
            if ($masuk->format('H:i:s') > '08:00:00') {
                $timeStr = $masuk->format('H:i:s');
                [$h, $m, $s] = explode(':', $timeStr);
                $totalSeconds = ($h * 3600) + ($m * 60) + $s;
                $limitSeconds = 8 * 3600;
                $diff = $totalSeconds - $limitSeconds;
                $totalMinutes = intdiv($diff, 60);

                if ($totalMinutes > 60) $tmCodeCalc = 'TM3';
                elseif ($totalMinutes > 30) $tmCodeCalc = 'TM2';
                else $tmCodeCalc = 'TM1';
            }
        } elseif (!in_array($this->kehadiran, ['HN', 'LN', 'LJ', 'TK'])) {
            $tmCodeCalc = 'TMDHM';
        }

        if ($this->jam_pulang) {
            $pulang = \Carbon\Carbon::parse($this->jam_pulang);
            $batasPulang = \Carbon\Carbon::parse($this->jam_pulang)->setTime(16, 30, 0);
            if ($pulang->format('H:i:s') < '16:30:00') {
                $timeStr = $pulang->format('H:i:s');
                [$h, $m, $s] = explode(':', $timeStr);
                $totalSeconds = ($h * 3600) + ($m * 60) + $s;
                $limitSeconds = (16 * 3600) + (30 * 60);
                $diff = $limitSeconds - $totalSeconds;
                $totalMinutes = intdiv($diff, 60);

                if ($totalMinutes > 60) $pcCodeCalc = 'PC3';
                elseif ($totalMinutes > 30) $pcCodeCalc = 'PC2';
                else $pcCodeCalc = 'PC1';
            }
        } elseif (!in_array($this->kehadiran, ['HN', 'LN', 'LJ', 'TK'])) {
            $pcCodeCalc = 'TMDHP';
        }

        // 2. Map Codes to Descriptions (NO CODES SHOWN)
        $descMap = [
            'TK'        => 'Tanpa Keterangan',
            'TMDHM'     => 'Tidak Absen Masuk',
            'TMDHP'     => 'Tidak Absen Pulang',
            'PC1-TMDHM' => 'Pulang Cepat Kurang dari 30 menit dan Tidak Absen Masuk',
            'TM1'       => 'Terlambat masuk',
            'TM2'       => 'Lebih dari 30 Menit',
            'TM3'       => 'Lebih dari 1 Jam',
            'TM'        => 'Terlambat',
            'PC'        => 'Pulang cepat',
            'PC1'       => 'Kurang dari 30 Menit',
            'PC2'       => 'Lebih dari 30 Menit',
            'PC3'       => 'Lebih dari 1 Jam',
            'S'         => 'Sakit',
            'I'         => 'Izin',
            'C'         => 'Cuti',
            'DL'        => 'Dinas Luar',
        ];

        $finalDescs = [];

        // Main Attendance Code
        if (isset($descMap[$this->kehadiran])) {
            $finalDescs[] = $descMap[$this->kehadiran];
        } else {
            $finalDescs[] = $this->kehadiran; // Fallback
        }

        // Secondary Calculated Codes
        if (!str_contains($this->kehadiran, 'PC') && $pcCodeCalc) {
            if (!str_starts_with($pcCodeCalc, 'TMD') && $pcCodeCalc !== $this->kehadiran) {
                $finalDescs[] = $descMap[$pcCodeCalc] ?? $pcCodeCalc;
            }
        }
        if (!str_contains($this->kehadiran, 'TM') && $tmCodeCalc) {
            if (!str_starts_with($tmCodeCalc, 'TMD') && $tmCodeCalc !== $this->kehadiran) {
                $finalDescs[] = $descMap[$tmCodeCalc] ?? $tmCodeCalc;
            }
        }

        return implode(' dan ', array_unique($finalDescs));
    }
}

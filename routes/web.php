<?php

use App\Livewire\AttendanceDashboard;
use App\Livewire\DataAbsensi;
use App\Livewire\RekapAbsensi;
use App\Livewire\MasterPeserta;
use App\Livewire\MasterKedeputian;
use Illuminate\Support\Facades\Route;
use App\Livewire\LogAktivitas;

Route::get('/', AttendanceDashboard::class)->name('dashboard');
Route::get('/absensi/import', DataAbsensi::class)->name('absensi.import');
Route::get('/absensi/rekap', RekapAbsensi::class)->name('absensi.rekap');
Route::get('/master/peserta', MasterPeserta::class)->name('master.peserta');
Route::get('/master/kedeputian', MasterKedeputian::class)->name('master.kedeputian');
Route::get('/log-aktivitas', LogAktivitas::class)->name('log-aktivitas');

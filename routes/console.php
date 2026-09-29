<?php

use App\Console\Commands\FetchAllBeritaOpd;
use App\Console\Commands\GenerateRingkasanPeriodik;
use App\Console\Commands\ProcessPendingBerita;
use Illuminate\Support\Facades\Schedule;

// Fetch data OPD — jalan terus-menerus sepanjang hari (pengumpulan)
Schedule::command(FetchAllBeritaOpd::class)->everyTwoHours();

// Proses AI — 3 titik waktu tetap (batch)
Schedule::command(ProcessPendingBerita::class)->dailyAt('08:05');
Schedule::command(ProcessPendingBerita::class)->dailyAt('12:05');
Schedule::command(ProcessPendingBerita::class)->dailyAt('18:05');

// Ringkasan mingguan — Hari Minggu, jam 18:00
Schedule::command(GenerateRingkasanPeriodik::class, ['mingguan'])->weeklyOn(0, '18:00');

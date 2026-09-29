<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_proses', function (Blueprint $table) {
            $table->id();
            $table->string('nama_proses'); // 'process_pending_berita', 'generate_ringkasan_harian', dll
            $table->string('status'); // 'sukses', 'gagal', 'kosong' (gak ada yang diproses)
            $table->integer('jumlah_diproses')->default(0);
            $table->text('keterangan')->nullable(); // detail/pesan error kalau ada
            $table->timestamp('dijalankan_pada');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_proses');
    }
};
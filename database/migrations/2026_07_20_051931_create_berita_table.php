<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('berita', function (Blueprint $table) {

        $table->id();

        $table->foreignId('opd_id')
            ->constrained('opd')
            ->cascadeOnDelete();


        $table->string('source_id');


        $table->string('judul');


        $table->text('isi');


        $table->text('ringkasan')
            ->nullable();


        $table->foreignId('kategori_id')
            ->nullable()
            ->constrained('kategori')
            ->nullOnDelete();


        $table->foreignId('kategori_asal_ai')
            ->nullable()
            ->constrained('kategori')
            ->nullOnDelete();


        $table->boolean('dikoreksi_manual')
            ->default(false);


        $table->foreignId('dikoreksi_oleh')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();


        $table->date('tanggal_publish')
            ->nullable();


        $table->string('link_asal')
            ->nullable();


        $table->timestamps();


        $table->unique([
            'opd_id',
            'source_id'
        ]);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beritas');
    }
};

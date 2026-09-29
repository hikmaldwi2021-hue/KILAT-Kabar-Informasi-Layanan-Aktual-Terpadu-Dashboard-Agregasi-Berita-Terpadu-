<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ringkasan_periodik', function (Blueprint $table) {

            $table->id();

            // harian, mingguan, bulanan
            $table->string('tipe');


            $table->date('periode_mulai');

            $table->date('periode_akhir');


            // hasil narasi AI
            $table->text('narasi');


            // data pendukung untuk card statistik
            $table->json('data_agregat')
                ->nullable();


            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('ringkasan_periodiks');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fetch_logs', function (Blueprint $table) {

            $table->id();


            $table->foreignId('opd_id')
                ->constrained('opd')
                ->cascadeOnDelete();


            // sukses / gagal
            $table->string('status');


            // pesan error atau informasi fetch
            $table->text('pesan')
                ->nullable();


            $table->timestamp('dijalankan_pada');


            // opsional, siapa yang trigger retry
            $table->foreignId('triggered_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();


            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('fetch_logs');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_clients', function (Blueprint $table) {

            $table->id();


            $table->string('nama_sistem');


            // token API
            $table->string('token')
                ->unique();


            $table->boolean('status_aktif')
                ->default(true);


            $table->timestamp('last_used_at')
                ->nullable();


            // admin pembuat token
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();


            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('api_clients');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('berita', function (Blueprint $table) {
            $table->string('thumbnail')->nullable()->after('link_asal');
            $table->json('galeri')->nullable()->after('thumbnail');
            $table->string('penulis')->nullable()->after('galeri');
            $table->string('fotografer')->nullable()->after('penulis');
            $table->string('editor')->nullable()->after('fotografer');
            $table->string('sumber')->nullable()->after('editor');
            $table->string('bidang_informasi')->nullable()->after('sumber');
            $table->string('kategori_info_publik')->nullable()->after('bidang_informasi');
        });
    }

    public function down(): void
    {
        Schema::table('berita', function (Blueprint $table) {
            $table->dropColumn([
                'thumbnail',
                'galeri',
                'penulis',
                'fotografer',
                'editor',
                'sumber',
                'bidang_informasi',
                'kategori_info_publik',
            ]);
        });
    }
};
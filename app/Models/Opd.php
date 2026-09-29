<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opd extends Model
{
    protected $table = 'opd';

    protected $fillable = [
        'nama',
        'kode',
        'endpoint_api',
        'status_aktif',
    ];


    protected $casts = [
        'status_aktif' => 'boolean',
    ];


    public function berita()
    {
        return $this->hasMany(Berita::class);
    }


    public function fetchLogs()
    {
        return $this->hasMany(FetchLog::class);
    }
}
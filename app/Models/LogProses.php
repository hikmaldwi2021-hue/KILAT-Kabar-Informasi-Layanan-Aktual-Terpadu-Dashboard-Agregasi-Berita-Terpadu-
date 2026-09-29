<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogProses extends Model
{
    protected $table = 'log_proses';

    protected $fillable = [
        'nama_proses',
        'status',
        'jumlah_diproses',
        'keterangan',
        'detail',
        'dijalankan_pada',
    ];

    protected $casts = [
        'dijalankan_pada' => 'datetime',
        'detail' => 'array',
    ];
}
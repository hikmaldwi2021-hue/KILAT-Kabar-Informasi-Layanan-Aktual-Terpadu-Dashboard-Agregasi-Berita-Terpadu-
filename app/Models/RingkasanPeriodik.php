<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RingkasanPeriodik extends Model
{

    protected $table = 'ringkasan_periodik';


    protected $fillable = [

        'tipe',

        'periode_mulai',

        'periode_akhir',

        'narasi',

        'data_agregat',

    ];



    protected $casts = [

        'periode_mulai' => 'date',

        'periode_akhir' => 'date',

        'data_agregat' => 'array',

    ];
}
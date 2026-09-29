<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FetchLog extends Model
{
    protected $table = 'fetch_logs';


    protected $fillable = [

        'opd_id',

        'status',

        'pesan',

        'dijalankan_pada',

        'triggered_by',

    ];


    protected $casts = [

        'dijalankan_pada' => 'datetime',

    ];



    public function opd()
    {
        return $this->belongsTo(Opd::class);
    }



    public function triggeredBy()
    {
        return $this->belongsTo(
            User::class,
            'triggered_by'
        );
    }
}
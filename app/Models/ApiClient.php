<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiClient extends Model
{

    protected $table = 'api_clients';


    protected $fillable = [

        'nama_sistem',

        'token',

        'status_aktif',

        'last_used_at',

        'created_by',

    ];



    protected $casts = [

        'status_aktif' => 'boolean',

        'last_used_at' => 'datetime',

    ];



    public function createdBy()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}
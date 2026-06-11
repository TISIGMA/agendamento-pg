<?php

namespace Labsoft\Models;

use Illuminate\Database\Eloquent\Model;

class TruckType extends Model
{
    protected $table = 'tipoVeiculo';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'descricao',
    ];
}

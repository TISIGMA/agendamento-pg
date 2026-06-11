<?php

namespace Labsoft\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingCompany extends Model
{
    protected $table = 'transportadora';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'username',
        'cnpj',
        'email',
        'telefone',
        'password',
        'data',
        'usuario',
        'celular',
        'cliente_origem',
    ];
}

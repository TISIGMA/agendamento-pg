<?php

namespace Labsoft\Models;

use Illuminate\Database\Eloquent\Model;

class OperationType extends Model
{
    protected $table = 'operation_type';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'label',
        'cliente',
        'operation_source_id',
    ];
}

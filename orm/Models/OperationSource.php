<?php

namespace Labsoft\Models;

use Illuminate\Database\Eloquent\Model;

class OperationSource extends Model
{
    protected $table = 'operation_source';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'label',
        'cliente',
    ];
}

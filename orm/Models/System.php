<?php

namespace Labsoft\Models;

use Illuminate\Database\Eloquent\Model;

class System extends Model
{
    protected $table = 'systems';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'systemUrl',
        'iconPath',
    ];
}

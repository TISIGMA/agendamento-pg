<?php

namespace Labsoft\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employee';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'position',
        'created_date',
        'created_by',
        'last_modified_date',
        'last_modified_by',
    ];
}

<?php

namespace Labsoft\Models;

use Illuminate\Database\Eloquent\Model;

class UserSystem extends Model
{
    protected $table = 'userSystems';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'userId',
        'systemsId',
    ];
}

<?php

namespace Labsoft\Models;

use Illuminate\Database\Eloquent\Model;

class ColumnsPreference extends Model
{
    protected $table = 'columns_preference';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'preference',
        'userId',
    ];
}

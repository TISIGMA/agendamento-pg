<?php

namespace Labsoft\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $table = 'attachment';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'type',
        'path',
        'scheduleId',
        'created_by',
        'created_date',
    ];
}

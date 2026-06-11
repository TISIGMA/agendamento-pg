<?php

namespace Labsoft\Models;

use Illuminate\Database\Eloquent\Model;

class SystemErrorInfo extends Model
{
    protected $table = 'system_error_info';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'contact_email',
        'created_date',
        'attachment_name',
        'description',
        'status',
        'resolution',
    ];
}

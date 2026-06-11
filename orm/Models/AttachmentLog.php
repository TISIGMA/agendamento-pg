<?php

namespace Labsoft\Models;

use Illuminate\Database\Eloquent\Model;

class AttachmentLog extends Model
{
    protected $table = 'attachment_log';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'path',
        'shipmentId',
        'created_date',
        'type',
        'action',
        'user_action',
        'date_time_action',
    ];
}

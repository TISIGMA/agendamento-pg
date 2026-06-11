<?php

namespace Labsoft\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleLog extends Model
{
    protected $table = 'janela_log';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'status',
        'tipoVeiculo',
        'placa_carreta',
        'operacao',
        'nf',
        'doca',
        'usuario',
        'dataInclusao',
        'inicio_operacao',
        'horaChegada',
        'fim_operacao',
        'saida',
        'transportadora',
        'placa_cavalo',
        'peso',
        'data_agendamento',
        'separacao',
        'shipment_id',
        'do_s',
        'cidade',
        'carga_qtde',
        'observacao',
        'dados_gerais',
        'cliente',
        'nome_motorista',
        'placa_carreta2',
        'documento_motorista',
        'operator',
        'checker',
        'action',
        'user_action',
        'date_time_action',
        'schedule_id',
        'attatchment_picking_status',
        'attatchment_invoice_status',
        'attatchment_certificate_status',
        'attatchment_boarding_status',
        'attatchment_other_status',
        'operation_type_id',
        'last_modified_by',
        'last_modified_date',
    ];
}

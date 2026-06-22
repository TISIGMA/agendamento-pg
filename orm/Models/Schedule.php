<?php

namespace Labsoft\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'janela';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'data_agendamento',
        'transportadora',
        'status',
        'tipoVeiculo',
        'placa_cavalo',
        'operacao',
        'nf',
        'horaChegada',
        'inicio_operacao',
        'fim_operacao',
        'usuario',
        'dataInclusao',
        'peso',
        'saida',
        'separacao',
        'shipment_id',
        'do_s',
        'cidade',
        'carga_qtde',
        'observacao',
        'dados_gerais',
        'cliente',
        'doca',
        'nome_motorista',
        'placa_carreta2',
        'documento_motorista',
        'placa_carreta',
        'operation_type_id',
        'operator',
        'checker',
        'created_date',
        'last_modified_by',
        'last_modified_date',
        'attatchment_picking_status',
        'attatchment_invoice_status',
        'attatchment_certificate_status',
        'attatchment_boarding_status',
        'attatchment_other_status',
        'scaneado',
        'carga_em_qualidade',
        'carregando_ou_rejeitado',
        'documentos',
    ];
}

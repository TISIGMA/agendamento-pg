
<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Labsoft\Models\Attachment;

class AttachmentRepository{

    private $mySql;

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findByScheduleId($scheduleId){

        try{
            if ($this->canUseEloquent()) {
                return Attachment::query()
                    ->select(['id', 'type', 'path', 'scheduleId', 'created_by', 'created_date'])
                    ->where('scheduleId', (int) $scheduleId)
                    ->get()
                    ->toArray();
            }

            $sql = "SELECT id, type, path, scheduleId, created_by, created_date
                    FROM attachment
                    WHERE scheduleId = ".$scheduleId;  

            $result = $this->mySql->query($sql);

            return $result;

        }catch(Exception $e){
            return false;
        }
    }

    public function findByShipmentId($shipmentId){

        try{
            if ($this->canUseEloquent()) {
                return Capsule::table('attachment as at')
                    ->select([
                        'at.id',
                        'type',
                        'path',
                        'scheduleId',
                        'created_by',
                        'at.created_date',
                        'j.shipment_id AS shipment_id',
                    ])
                    ->join('janela as j', 'j.id', '=', 'scheduleId')
                    ->where('j.shipment_id', $shipmentId)
                    ->orderBy('created_date', 'asc')
                    ->get()
                    ->map(function($row){ return (array) $row; })
                    ->toArray();
            }

            $sql = "SELECT at.id, type, path, scheduleId, created_by, at.created_date, j.shipment_id AS shipment_id 
                    FROM attachment AS at 
                    INNER JOIN janela AS j ON j.id = scheduleId
                    WHERE j.shipment_id = '".$shipmentId."'
                    ORDER BY created_date ASC";

            $result = $this->mySql->query($sql);
            return $result;
        }catch(Exception $e){
            return false;
        }
    }

    public function findByStartDateAndEndDate($startDate, $endDate){

        try{
            if ($this->canUseEloquent()) {
                return Capsule::table('attachment as at')
                    ->select([
                        'at.id',
                        'type',
                        'path',
                        'scheduleId',
                        'created_by',
                        'at.created_date',
                        'j.shipment_id AS shipment_id',
                    ])
                    ->join('janela as j', 'j.id', '=', 'scheduleId')
                    ->where('j.created_date', '>=', $startDate)
                    ->where('j.created_date', '<=', $endDate)
                    ->orderBy('at.id')
                    ->orderBy('at.created_date', 'asc')
                    ->get()
                    ->map(function($row){ return (array) $row; })
                    ->toArray();
            }

            $sql = "SELECT at.id, type, path, scheduleId, created_by, at.created_date, j.shipment_id AS shipment_id 
                    FROM attachment AS at 
                    INNER JOIN janela AS j ON j.id = scheduleId
                    WHERE j.created_date >= '".$startDate."' AND j.created_date <= '".$endDate."' 
                    ORDER BY at.id, at.created_date ASC";

            $result = $this->mySql->query($sql);
            return $result;
        }catch(Exception $e){
            return false;
        }
    }

    public function save($scheduleId, $path, $type){

        try{
            if ($this->canUseEloquent()) {
                Attachment::query()->create([
                    'scheduleId' => (int) $scheduleId,
                    'created_by' => $_SESSION['nome'],
                    'type' => $type,
                    'created_date' => date('Y-m-d H:i:s'),
                    'path' => $path,
                ]);
                return 'SAVED';
            }

            $sql = "INSERT INTO attachment
                    SET 
                    scheduleId = '".$scheduleId."',
                    created_by = '".$_SESSION['nome']."',
                    type = '".$type."',
                    created_date = '".date('Y-m-d H:i:s')."',
                    path = '".$path."'";  

            $result = $this->mySql->query($sql);
            return 'SAVED';

        }catch(Exception $e){
            return 'SAVE_ERROR';
        }
    }

    public function deleteByCondition($condition){

        try{
            if ($this->canUseEloquent()) {
                $ids = array_filter(array_map('intval', explode(',', $condition)));

                if (count($ids) > 0) {
                    Attachment::query()->whereIn('id', $ids)->delete();
                }

                return 'DELETED';
            }

            $sql = "DELETE FROM attachment
                    WHERE id IN (".$condition.")";  

            $result = $this->mySql->query($sql);
            return 'DELETED';

        }catch(Exception $e){
            return 'DELETE_ERROR';
        }
    }

    private function canUseEloquent(){
        return class_exists(Attachment::class) && class_exists(Capsule::class);
    }
}
?>

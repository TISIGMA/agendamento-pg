
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

    public function hasAttachmentsByType($scheduleId, $type){
        try{
            if ($this->canUseEloquent()) {
                $count = Attachment::query()
                    ->where('scheduleId', (int) $scheduleId)
                    ->where('type', $type)
                    ->count();
                return $count > 0;
            }

            $sql = "SELECT COUNT(*) AS total
                    FROM attachment
                    WHERE scheduleId = ".$scheduleId." AND type = '".$type."'";
            $result = $this->mySql->query($sql);
            $row = $result->fetch_assoc();
            return $row['total'] > 0;

        }catch(Exception $e){
            return false;
        }
    }

    public function getAttachmentsByScheduleIds($scheduleIds){
        try {
            if (empty($scheduleIds)) {
                return [];
            }

            // Converte para inteiros para segurança
            $safeIds = array_map('intval', $scheduleIds);
            $idsList = implode(',', $safeIds);

            if ($this->canUseEloquent()) {
                $attachments = Attachment::query()
                    ->whereIn('scheduleId', $safeIds)
                    ->select('scheduleId', 'type')
                    ->get();
                
                $result = [];
                foreach ($attachments as $attachment) {
                    if (!isset($result[$attachment->scheduleId])) {
                        $result[$attachment->scheduleId] = [];
                    }
                    $result[$attachment->scheduleId][$attachment->type] = true;
                }
                return $result;
            }

            $sql = "SELECT scheduleId, type FROM attachment WHERE scheduleId IN ($idsList)";
            $resultSet = $this->mySql->query($sql);
            
            $result = [];
            if ($resultSet) {
                while ($row = $resultSet->fetch_assoc()) {
                    $scheduleId = $row['scheduleId'];
                    $type = $row['type'];
                    if (!isset($result[$scheduleId])) {
                        $result[$scheduleId] = [];
                    }
                    $result[$scheduleId][$type] = true;
                }
            }
            return $result;

        } catch(Exception $e) {
            return [];
        }
    }

    private function canUseEloquent(){
        return class_exists(Attachment::class) && class_exists(Capsule::class);
    }
}
?>

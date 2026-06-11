
<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Labsoft\Models\AttachmentLog;

class AttachmentLogRepository{

    private $mySql;

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findByShipmentId($shipmentId){

        if ($this->canUseEloquent()) {
            return AttachmentLog::query()
                ->select(['id', 'path', 'shipmentId', 'created_date', 'type', 'action', 'user_action', 'date_time_action'])
                ->where('shipmentId', $shipmentId)
                ->orderBy('date_time_action', 'asc')
                ->get()
                ->toArray();
        }

        $sql = "SELECT id, path, shipmentId, created_date, type, action, user_action, date_time_action
                FROM attachment_log
                WHERE shipmentId = '".$shipmentId."' 
                ORDER BY date_time_action ASC";

        return $this->mySql->query($sql);
    }

    public function findByShipmentIds($shipmentIds){

        if ($this->canUseEloquent()) {
            return AttachmentLog::query()
                ->select(['id', 'path', 'shipmentId', 'created_date', 'type', 'action', 'user_action', 'date_time_action'])
                ->whereIn('shipmentId', $shipmentIds)
                ->orderBy('date_time_action', 'asc')
                ->get()
                ->toArray();
        }

        $sql = "SELECT id, path, shipmentId, created_date, type, action, user_action, date_time_action
                FROM attachment_log
                WHERE shipmentId IN ('".implode("','",$shipmentIds)."')
                ORDER BY date_time_action ASC";

        return $this->mySql->query($sql);
    }


    public function updateLastCreated($shipmentId, $qtdeRecords){

        try{
            if ($this->canUseEloquent()) {
                $ids = Capsule::table('attachment_log')
                    ->select(['id'])
                    ->orderBy('id', 'desc')
                    ->limit((int) $qtdeRecords)
                    ->get()
                    ->pluck('id')
                    ->toArray();

                if (count($ids) > 0) {
                    Capsule::table('attachment_log')
                        ->whereIn('id', $ids)
                        ->update([
                            'user_action' => $_SESSION['nome'],
                            'shipmentId' => $shipmentId,
                        ]);
                }

                return 'UPDATED';
            }

            $sql = "UPDATE attachment_log
                    SET user_action = '".$_SESSION['nome']."',
                    shipmentId = '".$shipmentId."'
                    ORDER BY id DESC LIMIT ".$qtdeRecords; 

            $result = $this->mySql->query($sql);
            return 'UPDATED';

        }catch(Exception $e){
            return 'UPDATE_ERROR';
        }
    }

    private function canUseEloquent(){
        return class_exists(AttachmentLog::class) && class_exists(Capsule::class);
    }
}
?>


<?php
class AttachmentLogRepository{

    private $mySql;

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findByShipmentId($shipmentId){

        $sql = "SELECT id, path, shipmentId, created_date, type, action, user_action, date_time_action
                FROM attachment_log
                WHERE shipmentId = '".$shipmentId."' 
                ORDER BY date_time_action ASC";

        return $this->mySql->query($sql);
    }

    public function findByShipmentIds($shipmentIds){

        $sql = "SELECT id, path, shipmentId, created_date, type, action, user_action, date_time_action
                FROM attachment_log
                WHERE shipmentId IN ('".implode("','",$shipmentIds)."')
                ORDER BY date_time_action ASC";

        return $this->mySql->query($sql);
    }


    public function updateLastCreated($shipmentId, $qtdeRecords){

        try{
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
}
?>
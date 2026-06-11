
<?php
class AttachmentRepository{

    private $mySql;

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findByScheduleId($scheduleId){

        try{
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
            $sql = "DELETE FROM attachment
                    WHERE id IN (".$condition.")";  

            $result = $this->mySql->query($sql);
            return 'DELETED';

        }catch(Exception $e){
            return 'DELETE_ERROR';
        }
    }
}
?>
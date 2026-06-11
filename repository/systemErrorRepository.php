
<?php
class SystemErrorRepository{

    private $mySql;

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findAll(){

        try{
            $sql = "SELECT system_error_info.id AS errorId,user_id,contact_email,created_date,attachment_name,description,status,resolution,usuario.nome AS user_name
                    FROM system_error_info
                    INNER JOIN usuario ON user_id = usuario.id
                    ORDER BY system_error_info.id DESC";  

            $result = $this->mySql->query($sql);

            return $result;

        }catch(Exception $e){
            return false;
        }
    }

    public function findById($id){

        try{
            $sql = "SELECT system_error_info.id AS errorId,user_id,contact_email,created_date,attachment_name,description,status,resolution,usuario.nome AS user_name
                    FROM system_error_info
                    INNER JOIN usuario ON user_id = usuario.id
                    WHERE system_error_info.id = '".$id."'
                    ORDER BY system_error_info.id DESC";  

            return $this->mySql->query($sql);

        }catch(Exception $e){
            return false;
        }
    }

    public function findByIdAndUserId($id, $userId){

        try{
            $sql = "SELECT system_error_info.id AS errorId,user_id,contact_email,created_date,attachment_name,description,status,resolution,usuario.nome AS user_name
                    FROM system_error_info
                    INNER JOIN usuario ON user_id = usuario.id
                    WHERE system_error_info.id = '".$id."' AND user_id = '".$userId."'
                    ORDER BY system_error_info.id DESC";  

            return $this->mySql->query($sql);

        }catch(Exception $e){
            return false;
        }
    }

    public function findByUserId($userId){

        try{
            $sql = "SELECT system_error_info.id AS errorId,user_id,contact_email,created_date,attachment_name,description,status,resolution,usuario.nome AS user_name
                    FROM system_error_info
                    INNER JOIN usuario ON user_id = usuario.id
                    WHERE user_id = '".$userId."'
                    ORDER BY system_error_info.id DESC";  

            return $this->mySql->query($sql);

        }catch(Exception $e){
            return false;
        }
    }


    public function save($systemError){

        try{
            $sql = "INSERT INTO system_error_info 
                    SET user_id = '".$systemError->getUserId()."', contact_email = '".$systemError->getEmail()."', attachment_name = '".$systemError->getFileName()."', description = '".$systemError->getDescription()."', status = 'Aguardando atendimento', created_date = '".$systemError->getCreatedDate()."'";  

            $result = $this->mySql->query($sql);

            if(!$result) return 'SAVE_ERROR';

            return $this->mySql->insert_id;

        }catch(Exception $e){
            return 'SAVE_ERROR';
        }
    }

    public function updateById($systemError, $id){

        try{
            $sql = "UPDATE system_error_info
                    SET user_id = '".$systemError->getUserId()."', contact_email = '".$systemError->getEmail()."', description = '".$systemError->getDescription()."', status = 'ABERTO', created_date = '".$systemError->getCreatedDate()."'";
            $sql .= ($systemError->getFileName() == null) ? '' : ", attachment_name = '".$systemError->getFileName()."'";
            $sql .= " WHERE ID = ".$id; 

            $result = $this->mySql->query($sql);
            return 'UPDATED';

        }catch(Exception $e){
            return 'SAVE_ERROR';
        }
    }
}
?>
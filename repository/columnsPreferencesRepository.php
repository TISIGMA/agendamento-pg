
<?php
class ColumnsPreferencesRepository{

    private $mySql;

    public function __construct($mySql){
        $this->mySql = $mySql;
    }


    public function findByUser($userId){

        try{
            $sql = "SELECT id,preference, userId
                    FROM columns_preference
                    WHERE userId = '".$userId."'";

            return $this->mySql->query($sql);

        }catch(Exception $e){
            return false;
        }
    }

    public function save($columnPreference){

        try{
            $sql = "INSERT INTO columns_preference
                    SET 
                    preference = '".$columnPreference->getPreference()."',
                    userId = ".$columnPreference->getUserId();
                  

            $result = $this->mySql->query($sql);
            return 'SAVED';

        }catch(Exception $e){
            return 'SAVE_ERROR';
        }
    }

    public function updateById($columnPreference, $id){

        try{
            $sql = "UPDATE columns_preference
                    SET
                    preference = '".$columnPreference->getPreference()."',
                    userId = ".$columnPreference->getUserId()."
                    WHERE id = ".$id;  

            $result = $this->mySql->query($sql);
            return 'UPDATED';

        }catch(Exception $e){
            return 'SAVE_ERROR';
        }
    }
}
?>
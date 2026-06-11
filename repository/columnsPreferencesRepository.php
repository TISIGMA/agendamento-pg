
<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Labsoft\Models\ColumnsPreference;

class ColumnsPreferencesRepository{

    private $mySql;

    public function __construct($mySql){
        $this->mySql = $mySql;
    }


    public function findByUser($userId){

        try{
            if ($this->canUseEloquent()) {
                return ColumnsPreference::query()
                    ->select(['id', 'preference', 'userId'])
                    ->where('userId', (int) $userId)
                    ->get()
                    ->toArray();
            }

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
            if ($this->canUseEloquent()) {
                ColumnsPreference::query()->create([
                    'preference' => $columnPreference->getPreference(),
                    'userId' => (int) $columnPreference->getUserId(),
                ]);
                return 'SAVED';
            }

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
            if ($this->canUseEloquent()) {
                ColumnsPreference::query()
                    ->where('id', (int) $id)
                    ->update([
                        'preference' => $columnPreference->getPreference(),
                        'userId' => (int) $columnPreference->getUserId(),
                    ]);
                return 'UPDATED';
            }

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

    private function canUseEloquent(){
        return class_exists(ColumnsPreference::class) && class_exists(Capsule::class);
    }
}
?>

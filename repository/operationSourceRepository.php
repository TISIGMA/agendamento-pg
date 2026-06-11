
<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Labsoft\Models\OperationSource;

class OperationSourceRepository{

    private $mySql;

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findAll(){

        try{
            if ($this->canUseEloquent()) {
                return OperationSource::query()
                    ->select(['id', 'name', 'label'])
                    ->get()
                    ->toArray();
            }

            $sql = "SELECT id, name, label
                    FROM operation_source";  

            $result = $this->mySql->query($sql);

            return $result;

        }catch(Exception $e){
            return false;
        }
    }

    public function findByName($name){

        try{
            if ($this->canUseEloquent()) {
                return OperationSource::query()
                    ->select(['id', 'name', 'label'])
                    ->where('name', $name)
                    ->get()
                    ->toArray();
            }

            $sql = "SELECT id, name, label
                    FROM operation_source
                    WHERE name = '".$name."'";  

            return $this->mySql->query($sql);

        }catch(Exception $e){
            return false;
        }
    }

    public function save($name, $label, $client){

        try{
            if ($this->canUseEloquent()) {
                OperationSource::query()->create([
                    'name' => $name,
                    'label' => $label,
                    'cliente' => $client,
                ]);

                return 'SAVED';
            }

            $sql = "INSERT INTO operation_source
                    SET name = '".$name."', label = '".$label."', cliente = '".$client."'";  

            $result = $this->mySql->query($sql);
            return 'SAVED';

        }catch(Exception $e){
            return 'SAVE_ERROR';
        }
    }

    public function updateById($id, $name, $label){

        try{
            if ($this->canUseEloquent()) {
                OperationSource::query()
                    ->where('id', (int) $id)
                    ->update([
                        'name' => $name,
                        'label' => $label,
                    ]);

                return 'UPDATED';
            }

            $sql = "UPDATE operation_source
                    SET name = '".$name."', label = '".$label."'
                    WHERE ID = ".$id;  

            $result = $this->mySql->query($sql);
            return 'UPDATED';

        }catch(Exception $e){
            return 'UPDATE_ERROR';
        }
    }

    public function deleteById($id){

        try{
            if ($this->canUseEloquent()) {
                OperationSource::query()
                    ->where('id', (int) $id)
                    ->delete();

                return 'DELETED';
            }

            $sql = "DELETE FROM operation_source
                    WHERE id = ".$id;  

            $result = $this->mySql->query($sql);
            return 'DELETED';

        }catch(Exception $e){
            return 'DELETE_ERROR';
        }
    }

    private function canUseEloquent(){
        return class_exists(OperationSource::class) && class_exists(Capsule::class);
    }
}
?>

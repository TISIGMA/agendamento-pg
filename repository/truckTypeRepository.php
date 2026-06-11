
<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Labsoft\Models\TruckType;

class TruckTypeRepository{

    private $mySql;

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findAll(){

        try{
            if ($this->canUseEloquent()) {
                return TruckType::query()
                    ->select(['id', 'descricao'])
                    ->get()
                    ->toArray();
            }

            $sql = "SELECT id, descricao
                    FROM tipoVeiculo";  

            $result = $this->mySql->query($sql);

            return $result;

        }catch(Exception $e){
            return false;
        }
    }

    public function findByDescription($description){

        try{
            if ($this->canUseEloquent()) {
                return TruckType::query()
                    ->select(['id', 'descricao'])
                    ->where('descricao', $description)
                    ->get()
                    ->toArray();
            }

            $sql = "SELECT id, descricao
                    FROM tipoVeiculo
                    WHERE descricao = '".$description."'";  

            return $this->mySql->query($sql);

        }catch(Exception $e){
            return false;
        }
    }

    public function save($post){

        try{
            if ($this->canUseEloquent()) {
                TruckType::query()->create([
                    'descricao' => $post['description'],
                ]);
                return 'SAVED';
            }

            $sql = "INSERT INTO tipoVeiculo
                    SET descricao = '".$post['description']."'";  

            $result = $this->mySql->query($sql);
            return 'SAVED';

        }catch(Exception $e){
            return 'SAVE_ERROR';
        }
    }

    public function updateById($id, $description){

        try{
            if ($this->canUseEloquent()) {
                TruckType::query()
                    ->where('id', (int) $id)
                    ->update([
                        'descricao' => $description,
                    ]);
                return 'UPDATED';
            }

            $sql = "UPDATE tipoVeiculo
                    SET descricao = '".$description."'
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
                TruckType::query()
                    ->where('id', (int) $id)
                    ->delete();
                return 'DELETED';
            }

            $sql = "DELETE FROM tipoVeiculo
                    WHERE id = ".$id;  

            $result = $this->mySql->query($sql);
            return 'DELETED';

        }catch(Exception $e){
            return 'DELETE_ERROR';
        }
    }

    private function canUseEloquent(){
        return class_exists(TruckType::class) && class_exists(Capsule::class);
    }
}
?>

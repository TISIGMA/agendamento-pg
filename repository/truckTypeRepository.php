
<?php
class TruckTypeRepository{

    private $mySql;

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findAll(){

        try{
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
            $sql = "DELETE FROM tipoVeiculo
                    WHERE id = ".$id;  

            $result = $this->mySql->query($sql);
            return 'DELETED';

        }catch(Exception $e){
            return 'DELETE_ERROR';
        }
    }
}
?>
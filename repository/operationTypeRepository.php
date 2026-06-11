
<?php
class OperationTypeRepository{

    private $mySql;

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findAll(){

        try{
            $sql = "SELECT operation_type.id, operation_type.name, operation_type.label, operation_source_id AS operationSourceId, operation_source.name AS operationSourceName  
                    FROM operation_type
                    INNER JOIN operation_source ON (operation_source.id = operation_source_id || operation_source_id IS NULL)";

            $result = $this->mySql->query($sql);

            return $result;

        }catch(Exception $e){
            return false;
        }
    }

    public function findByClient($client){

        try{
            $sql = "SELECT operation_type.id, operation_type.name, operation_type.label, operation_source_id AS operationSourceId, operation_source.name AS operationSourceName  
                    FROM operation_type
                    INNER JOIN operation_source ON (operation_source.id = operation_source_id || operation_source_id IS NULL)
                    WHERE operation_type.cliente = '".$client."'";  

            return $this->mySql->query($sql);

        }catch(Exception $e){
            return false;
        }
    }

    public function findByName($name){

        try{
            $sql = "SELECT operation_type.id, operation_type.name, operation_type.label, operation_source_id AS operationSourceId, operation_source.name AS operationSourceName 
                    FROM operation_type
                    INNER JOIN operation_source ON (operation_source.id = operation_source_id || operation_source_id IS NULL)
                    WHERE operation_type.name = '".$name."'";  

            return $this->mySql->query($sql);

        }catch(Exception $e){
            return false;
        }
    }

    public function save($name, $label, $client, $operationSourceId){
        
        // adicionado busca do último id para inserir no registro novo
        // necessário devido ter subido a tabela sem autoincremento e não ser
        // possível alterar devido foreingkey
    

        try{
            
            $sql = "SELECT id FROM operation_type ORDER BY id DESC LIMIT 1";
            $result = $this->mySql->query($sql);
            
            $firstLine = $result->fetch_assoc();
            $newId = $firstLine['id'] + 1;
            
            $sql = "INSERT INTO operation_type
                    SET id = ".$newId.", name = '".$name."', label = '".$label."', cliente = '".$client."', operation_source_id = ".$operationSourceId ;  

            $result = $this->mySql->query($sql);
            return 'SAVED';

        }catch(Exception $e){
            echo 'erro : '. $e->getMessage();
            return 'SAVE_ERROR';
        }
    }

    public function updateById($id, $name, $label, $operationSourceId){

        try{
            $sql = "UPDATE operation_type
                    SET name = '".$name."', label = '".$label."', operation_source_id = ". $operationSourceId . "
                    WHERE ID = ".$id;  

            $result = $this->mySql->query($sql);
            return 'UPDATED';

        }catch(Exception $e){
            return 'UPDATE_ERROR';
        }
    }

    public function deleteById($id){

        try{
            $sql = "DELETE FROM operation_type
                    WHERE id = ".$id;  

            $result = $this->mySql->query($sql);
            return 'DELETED';

        }catch(Exception $e){
            return 'DELETE_ERROR';
        }
    }
}
?>
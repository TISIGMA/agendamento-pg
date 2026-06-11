<?php
class ShippingCompanyRepository{

    private $mySql;

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findByClient($clientName){

        try{
            $sql = "SELECT id, nome, username, cnpj, email, telefone, password, data, usuario, celular, cliente_origem 
                    FROM transportadora 
                    WHERE cliente_origem = '" .$clientName."'";  
                    
            $result = $this->mySql->query($sql);

            return $result;

        }catch(Exception $e){
            return false;
        }
    }
    
    public function findAll(){

        try{
            $sql = "SELECT id, nome, username, cnpj, email, telefone, password, data, usuario, celular, cliente_origem 
                    FROM transportadora";  
                    
            $result = $this->mySql->query($sql);

            return $result;

        }catch(Exception $e){
            return false;
        }
    }

    public function save($post){

        try{
            $sql = "INSERT INTO transportadora
                    SET nome = '".$post['name']."', cliente_origem = 'tetrapak', username = '', cnpj = '', email = '', telefone = '', password = '', data = '".date("y-m-d H:i:s")."', usuario = '".$_SESSION['nome']."', celular = ''";  

            $result = $this->mySql->query($sql);
            return 'SAVED';

        }catch(Exception $e){
            return 'SAVE_ERROR';
        }
    }

    public function updateById($id, $name){

        try{
            $sql = "UPDATE transportadora
                    SET nome = '".$name."'
                    WHERE ID = ".$id;  

            $result = $this->mySql->query($sql);
            return 'UPDATED';

        }catch(Exception $e){
            return 'UPDATE_ERROR';
        }
    }

    public function deleteById($id){

        try{
            $sql = "DELETE FROM transportadora
                    WHERE id = ".$id;  

            $result = $this->mySql->query($sql);
            return 'DELETED';

        }catch(Exception $e){
            return 'DELETE_ERROR';
        }
    }
}

?>

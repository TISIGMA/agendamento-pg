<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Labsoft\Models\ShippingCompany;

class ShippingCompanyRepository{

    private $mySql;

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findByClient($clientName){

        try{
            if ($this->canUseEloquent()) {
                return ShippingCompany::query()
                    ->select([
                        'id',
                        'nome',
                        'username',
                        'cnpj',
                        'email',
                        'telefone',
                        'password',
                        'data',
                        'usuario',
                        'celular',
                        'cliente_origem',
                    ])
                    ->where('cliente_origem', $clientName)
                    ->get()
                    ->toArray();
            }

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
            if ($this->canUseEloquent()) {
                return ShippingCompany::query()
                    ->select([
                        'id',
                        'nome',
                        'username',
                        'cnpj',
                        'email',
                        'telefone',
                        'password',
                        'data',
                        'usuario',
                        'celular',
                        'cliente_origem',
                    ])
                    ->get()
                    ->toArray();
            }

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
            if ($this->canUseEloquent()) {
                ShippingCompany::query()->create([
                    'nome' => $post['name'],
                    'cliente_origem' => 'tetrapak',
                    'username' => '',
                    'cnpj' => '',
                    'email' => '',
                    'telefone' => '',
                    'password' => '',
                    'data' => date('y-m-d H:i:s'),
                    'usuario' => $_SESSION['nome'],
                    'celular' => '',
                ]);

                return 'SAVED';
            }

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
            if ($this->canUseEloquent()) {
                ShippingCompany::query()
                    ->where('id', (int) $id)
                    ->update([
                        'nome' => $name,
                    ]);
                return 'UPDATED';
            }

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
            if ($this->canUseEloquent()) {
                ShippingCompany::query()
                    ->where('id', (int) $id)
                    ->delete();
                return 'DELETED';
            }

            $sql = "DELETE FROM transportadora
                    WHERE id = ".$id;  

            $result = $this->mySql->query($sql);
            return 'DELETED';

        }catch(Exception $e){
            return 'DELETE_ERROR';
        }
    }

    private function canUseEloquent(){
        return class_exists(ShippingCompany::class) && class_exists(Capsule::class);
    }
}

?>

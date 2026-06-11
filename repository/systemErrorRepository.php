
<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Labsoft\Models\SystemErrorInfo;

class SystemErrorRepository{

    private $mySql;

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findAll(){

        try{
            if ($this->canUseEloquent()) {
                return Capsule::table('system_error_info as sei')
                    ->select([
                        'sei.id AS errorId',
                        'user_id',
                        'contact_email',
                        'created_date',
                        'attachment_name',
                        'description',
                        'status',
                        'resolution',
                        'u.nome AS user_name',
                    ])
                    ->join('usuario as u', 'sei.user_id', '=', 'u.id')
                    ->orderBy('sei.id', 'desc')
                    ->get()
                    ->map(function($row){ return (array) $row; })
                    ->toArray();
            }

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
            if ($this->canUseEloquent()) {
                return Capsule::table('system_error_info as sei')
                    ->select([
                        'sei.id AS errorId',
                        'user_id',
                        'contact_email',
                        'created_date',
                        'attachment_name',
                        'description',
                        'status',
                        'resolution',
                        'u.nome AS user_name',
                    ])
                    ->join('usuario as u', 'sei.user_id', '=', 'u.id')
                    ->where('sei.id', (int) $id)
                    ->orderBy('sei.id', 'desc')
                    ->get()
                    ->map(function($row){ return (array) $row; })
                    ->toArray();
            }

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
            if ($this->canUseEloquent()) {
                return Capsule::table('system_error_info as sei')
                    ->select([
                        'sei.id AS errorId',
                        'user_id',
                        'contact_email',
                        'created_date',
                        'attachment_name',
                        'description',
                        'status',
                        'resolution',
                        'u.nome AS user_name',
                    ])
                    ->join('usuario as u', 'sei.user_id', '=', 'u.id')
                    ->where('sei.id', (int) $id)
                    ->where('sei.user_id', (int) $userId)
                    ->orderBy('sei.id', 'desc')
                    ->get()
                    ->map(function($row){ return (array) $row; })
                    ->toArray();
            }

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
            if ($this->canUseEloquent()) {
                return Capsule::table('system_error_info as sei')
                    ->select([
                        'sei.id AS errorId',
                        'user_id',
                        'contact_email',
                        'created_date',
                        'attachment_name',
                        'description',
                        'status',
                        'resolution',
                        'u.nome AS user_name',
                    ])
                    ->join('usuario as u', 'sei.user_id', '=', 'u.id')
                    ->where('sei.user_id', (int) $userId)
                    ->orderBy('sei.id', 'desc')
                    ->get()
                    ->map(function($row){ return (array) $row; })
                    ->toArray();
            }

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
            if ($this->canUseEloquent()) {
                $id = Capsule::table('system_error_info')->insertGetId([
                    'user_id' => $systemError->getUserId(),
                    'contact_email' => $systemError->getEmail(),
                    'attachment_name' => $systemError->getFileName(),
                    'description' => $systemError->getDescription(),
                    'status' => 'Aguardando atendimento',
                    'created_date' => $systemError->getCreatedDate(),
                ]);

                return $id;
            }

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
            if ($this->canUseEloquent()) {
                $data = [
                    'user_id' => $systemError->getUserId(),
                    'contact_email' => $systemError->getEmail(),
                    'description' => $systemError->getDescription(),
                    'status' => 'ABERTO',
                    'created_date' => $systemError->getCreatedDate(),
                ];

                if ($systemError->getFileName() != null) {
                    $data['attachment_name'] = $systemError->getFileName();
                }

                Capsule::table('system_error_info')
                    ->where('id', (int) $id)
                    ->update($data);

                return 'UPDATED';
            }

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

    private function canUseEloquent(){
        return class_exists(SystemErrorInfo::class) && class_exists(Capsule::class);
    }
}
?>

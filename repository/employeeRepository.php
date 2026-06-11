
<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Labsoft\Models\Employee;

class EmployeeRepository{

    private $mySql;

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findAll(){

        try{
            if ($this->canUseEloquent()) {
                return Capsule::table('employee as e')
                    ->select([
                        'e.id',
                        'e.name',
                        'e.position',
                        'e.created_date',
                        'u1.nome as user_name',
                        'e.last_modified_date',
                        'u2.nome as last_user_name',
                    ])
                    ->leftJoin('usuario as u1', 'e.created_by', '=', 'u1.id')
                    ->leftJoin('usuario as u2', 'e.last_modified_by', '=', 'u2.id')
                    ->orderBy('e.name', 'asc')
                    ->get()
                    ->toArray();
            }

            $sql = "SELECT e.id, name, position, created_date, 
                        (SELECT nome FROM usuario WHERE id = e.created_by) AS user_name, 
                        last_modified_date, 
                        (SELECT nome FROM usuario WHERE id = e.last_modified_by) AS last_user_name
                    FROM employee e
                    ORDER BY name ASC";

            $result = $this->mySql->query($sql);

            return $result;

        }catch(Exception $e){
            return false;
        }
    }

    public function findByName($name){

        try{
            if ($this->canUseEloquent()) {
                return Employee::query()
                    ->select(['id', 'name', 'position', 'created_date'])
                    ->where('name', $name)
                    ->get()
                    ->toArray();
            }

            $sql = "SELECT id, name, position, created_date
                    FROM employee
                    WHERE name = '".$name."'"; 

            $result = $this->mySql->query($sql);

            return $result;

        }catch(Exception $e){
            return false;
        }
    }

    public function findByNameAndPosition($name, $position){
        try{
            if ($this->canUseEloquent()) {
                return Employee::query()
                    ->select(['id', 'name', 'position', 'created_date'])
                    ->where('name', $name)
                    ->where('position', $position)
                    ->get()
                    ->toArray();
            }

            $sql = "SELECT id, name, position, created_date
                    FROM employee
                    WHERE name = '".$name."' 
                    AND position = '".$position."'"; 

            $result = $this->mySql->query($sql);

            return $result;

        }catch(Exception $e){
            return false;
        }
    }

    public function save($post){

        try{
            if ($this->canUseEloquent()) {
                Employee::query()->create([
                    'name' => $post['name'],
                    'position' => $post['position'],
                    'created_date' => date('Y-m-d H:i:s'),
                    'created_by' => $_SESSION['id'],
                ]);
                return 'SAVED';
            }

            $sql = "INSERT INTO employee (name, position, created_date, created_by)
                    VALUES(
                     '".$post['name']."',
                     '".$post['position']."',
                     '".date('Y-m-d H:i:s')."',
                     ".$_SESSION['id']."
                    )";


            $result = $this->mySql->query($sql);
            return 'SAVED';

        }catch(Exception $e){
            return 'SAVE_ERROR';
        }
    }

    public function updateById($id, $name, $position){

        try{
            if ($this->canUseEloquent()) {
                Employee::query()
                    ->where('id', (int) $id)
                    ->update([
                        'name' => $name,
                        'position' => $position,
                        'last_modified_date' => date('Y-m-d H:i:s'),
                        'last_modified_by' => $_SESSION['id'],
                    ]);
                return 'UPDATED';
            }

            $sql = "UPDATE employee
                    SET name = '".$name."', position = '".$position."', last_modified_date = '".date("Y-m-d H:i:s")."', last_modified_by = ".$_SESSION['id']." 
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
                Employee::query()
                    ->where('id', (int) $id)
                    ->delete();
                return 'DELETED';
            }

            $sql = "DELETE FROM employee
                    WHERE id = ".$id;  

            $result = $this->mySql->query($sql);
            return 'DELETED';

        }catch(Exception $e){
            return 'DELETE_ERROR';
        }
    }

    private function canUseEloquent(){
        return class_exists(Employee::class) && class_exists(Capsule::class);
    }
}
?>

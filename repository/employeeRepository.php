
<?php
class EmployeeRepository{

    private $mySql;

    public function __construct($mySql){
        $this->mySql = $mySql;
    }

    public function findAll(){

        try{
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
            $sql = "DELETE FROM employee
                    WHERE id = ".$id;  

            $result = $this->mySql->query($sql);
            return 'DELETED';

        }catch(Exception $e){
            return 'DELETE_ERROR';
        }
    }
}
?>
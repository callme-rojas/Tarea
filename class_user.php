<?php
    //print_r($stmt->errorInfo());
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    include_once 'db.php';
    class user_class extends Database {  
        //name of the table
        private $table_name = "user";
        //name of the table's columns
        public $id;
        public $nombre;
        public $marca;
        public $precio;
        public $material;

        //creation of the connection
        public function __construct(){    
            $this->conn = $this->getConnection();
        }

        //get all users
        public function getAllUsers()
        {
            $stmt = $this->conn->prepare("
            SELECT
            `relojes`.`id` as 'relojes_id', 
            `relojes`.`nombre` as 'relojes_nombre',
            `relojes`.`marca` as 'relojes_marca',
            `relojes`.`precio` as 'relojes_precio',
            `relojes`.`material` as 'relojes_material'
            FROM `relojes`
            ORDER by `relojes`.`id` ASC
            ");
            if ($stmt->execute())
                {
                    $result = array();
                    if($stmt->rowCount() > 0){
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                            $item = array(
                                        'relojes_id' => $row['relojes_id'],
                                        'relojes_nombre' => $row['relojes_nombre'],
                                        'relojes_marca' => $row['relojes_marca'],
                                        'relojes_precio' => $row['relojes_precio'],
                                        'relojes_material'=>$row['relojes_material']
                                         );
                            array_push($result , $item);
                        }
                    }
                    return $result;
                }
            else
                return false;        
        }

        //get a single user
        public function getUser($relojes_id,$marca)
        {
            $where_clause = '';
            if(isset($relojes_id))
                $where_clause = " WHERE `relojes`.`id`=  $relojes_id";

            if(isset($marca))
                $where_clause = " WHERE `relojes`.`marca` =  '$marca'";

            $stmt = $this->conn->prepare("
            SELECT
            `relojes`.`id` as 'relojes_id', 
            `relojes`.`nombre` as 'relojes_nombre',
            `relojes`.`marca` as 'relojes_marca',
            `relojes`.`precio` as 'relojes_precio',
            `relojes`.`material` as 'relojes_material'
            FROM `relojes`
            $where_clause
            ORDER by `relojes`.`id` ASC
            ");
            if ($stmt->execute())
                //return $stmt;
                {
                    $result = array();
                    if($stmt->rowCount() > 0){
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                            $item = array(
                                        'relojes_id' => $row['relojes_id'],
                                        'relojes_nombre' => $row['relojes_nombre'],
                                        'relojes_marca' => $row['relojes_marca'],
                                        'relojes_precio' => $row['relojes_precio'],
                                        'relojes_material' => $row['relojes_material']
                                         );
                            array_push($result , $item);
                        }
                    }
                    return $result;
                }
            else
                return false;        
        }
        
        //insert user
        public function insertUser( $nombre, $marca, $precio, $material){                  
            $stmt= $this->conn->prepare("INSERT INTO `relojes` (`id`, `nombre`, `marca`, `precio`, `material`) 
                                         VALUES (NULL, :nombre, :marca, :precio, :material);") ;
            $stmt->bindValue('nombre', $nombre);
            $stmt->bindValue('marca', $marca);
            $stmt->bindValue('precio', $precio);
            $stmt->bindValue('material', $material);
            
            if ($stmt->execute())
                return true;
            else
                return false;
             
 
        }
        
        //update user
        public function updateUser($id, $nombre, $marca, $precio,  $material){ 
            $stmt = $this->conn->prepare("SELECT id FROM `user` WHERE `user`.`id` = :id;");
            $stmt->execute(array(':id' => $id));
            if ($stmt->rowCount()) {
                $stmt = $this->conn->prepare("
                UPDATE `relojes` SET 
                `nombre` = :nombre, 
                `marca` = :marca, 
                `precio` = :precio, 
                `material` = :material 
                WHERE `relojes`.`id` = :id;
                ");

                $stmt->bindValue('id', $id);
                $stmt->bindValue('nombre', $nombre);
                $stmt->bindValue('marca', $marca);
                $stmt->bindValue('precio', $precio);
                $stmt->bindValue('material', $material);
               
                if ($stmt->execute())
                    return true;
                else
                    return false;
                }
            else
                return false;
                  
        }

        //update user
        public function deleteUser($id){
            $stmt = $this->conn->prepare("DELETE FROM `relojes` WHERE `relojes`.`id` = :id");
            if ($stmt->execute(array('id' => $id)))
                return true;
            else
                return false;         
        }
      }
?>

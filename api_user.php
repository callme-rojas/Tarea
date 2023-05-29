<?php 

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    header("Content-Type: application/json; charset=UTF-8");
    include_once '../class/class_user.php';
    
    $user = new user_class();

    switch($_SERVER['REQUEST_METHOD']){
        

        case 'GET': //get a single o list
            
            
            if(isset($_GET['id'])){
                $getId = $user->getUser($_GET['id'],null);
                if(is_array($getId))
                {
                    $result["data"] = $getId;
                    $result["status"] = 'success';
                }
                else
                {
                    $result["status"] = "error";
                    $result["message"] = "Unable to communicate with database";                       
                }
            }
            else
            
            if(isset($_GET['nombre'])){
                    $getId = $user->getUser(null,$_GET['nombre']);
                    if(is_array($getId))
                    {
                        $result["data"] = $getId;
                        $result["status"] = 'success';
                    }
                    else
                    {
                        $result["status"] = "error";
                        $result["message"] = "Unable to communicate with database";                       
                    }
                }
            else{
                $getAll = $user->getAllUsers();
                if(is_array($getAll))
                {
                    $result["data"] = $getAll;
                    $result["status"] = 'success';
                }
                else
                {
                    $result["status"] = "error";
                    $result["message"] = "Unable to communicate with database";                       
                } 
            }
        break;        

        case 'POST': //create
            $_POST = json_decode(file_get_contents('php://input'), true);
            if( $user->insertUser($_POST['nombre'],$_POST['marca'],
                                   $_POST['precio'],$_POST['material']))
            {
                $result["status"] = 'success';
                $result["data"] = null;
            }
            else{
                $result["status"] = 'error';
                $result["message"] = "Duplicate data or invalid data";
            }

        break;
       
        case 'DELETE': //delete
            $_DELETE = json_decode(file_get_contents('php://input'),true);
            $deleteId = $user->deleteUser($_DELETE['id']);
            if($deleteId)
            {
                $result["data"] = null;
                $result["status"] = 'success';    
            }
            else
            {
                $result["status"] = "error";
                $result["message"] = "Unable to communicate with database";                       
            } 
        break;
        
        case 'PUT': //update
            $_PUT = json_decode(file_get_contents('php://input'),true);
            $updateId =  $user->updateUser($_PUT['id'],$_PUT['nombre'],$_PUT['marca'],
            $_PUT['precio'],$_PUT['material']);
            if($updateId)
            {
                $result["data"] = null;
                $result["status"] = 'success';                
            }
            else
            {
                $result["status"] = "error";
                $result["message"] = "Unable to communicate with database or duplicate";                       
            } 
        break;   
        
        default:{
            $result["status"] = "error";
            $result["message"] = "Unknown request";
        }
    }
    echo json_encode($result);
?>
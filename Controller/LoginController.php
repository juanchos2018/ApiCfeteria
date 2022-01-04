<?php
require_once('../Model/Login.php');
require_once('../Includes/cors.php');
date_default_timezone_set('America/Lima');

$method=$_SERVER['REQUEST_METHOD'];

if($method=="POST"){     
    //if(!empty($_GET['COD_USUARIO'])){
        $data=json_decode(file_get_contents("php://input"),true);  
        $user= utf8_decode($data['COD_USUARIO']);
        $DES_PASSWORD= utf8_decode($data['DES_PASSWORD']);
        $p=utf8_encode($user);

        $obj=new Login();      
        $obj->DES_PASSWORD=$DES_PASSWORD;       
        $cod=json_encode($p, JSON_UNESCAPED_UNICODE); 
        $json =   json_decode($cod);
        $obj->COD_USUARIO =$json; 
        $result=$obj->Ingresar($obj);
        $json=json_encode($result,JSON_UNESCAPED_UNICODE);
        echo $json ;   
  //  }
         
}





  
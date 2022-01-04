<?php
require_once('../Model/Categoria.php');
require_once('../Includes/cors.php');

$method=$_SERVER['REQUEST_METHOD'];


if ($method=="POST"){
    $json=null;
    $obj =new  Categoria();   
    $data=json_decode(file_get_contents("php://input"),true);  
    $obj->nombre_categoria=$data['nombre_categoria'];    
    $obj->estado=$data['estado'];    
    $json=$obj->Store($obj);
    echo $json;  
}   


if($method=="GET"){     
    $api=new Categoria();
    $obj=$api->Get();
    $json=json_encode($obj);
    echo $json;        
}

if($method=="PUT"){
     
    $obj=new Categoria();   
    $data=json_decode(file_get_contents("php://input"),true);  
    $obj->id_categoria=$data['id_categoria'];    
    $obj->nombre_categoria=$data['nombre_categoria'];    
    $obj->estado=$data['estado'];  
    $json=$obj->Update($obj);
  
    $msg = new ArrayObject([], ArrayObject::ARRAY_AS_PROPS);                
    $msg['status'] =  "400";   
    $msg['error'] =  false; 
    $msg['msg'] =  $json; 
    echo json_encode($msg, JSON_UNESCAPED_UNICODE); 
  
    
}


  
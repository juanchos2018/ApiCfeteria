<?php
require_once('../Model/Producto.php');
require_once('../Includes/cors.php');

$method=$_SERVER['REQUEST_METHOD'];


if ($method=="POST"){
    $json=null;
    $obj =new  Producto();   
    $data=json_decode(file_get_contents("php://input"),true);  
    $obj->nombre_producto=$data['nombre_producto'];    
    $obj->id_categoria=$data['id_categoria'];   
    $obj->precio_producto=$data['precio_producto'];    
    $obj->estado=$data['estado'];    
    $obj->cantidad_producto=$data['cantidad_producto'];   
    $obj->descripcion=$data['descripcion'];    
    $obj->imagen=$data['imagen'];    

    $json=$obj->Store($obj);
    echo $json;  
}   


if($method=="GET"){    
    if (!empty($_GET['id_categoria'])) {
        $id_categoria=$_GET['id_categoria'];
        $api=new Producto();
        $obj=$api->Get($id_categoria);
        $json=json_encode($obj,JSON_UNESCAPED_UNICODE);
        echo $json;   
    } 
   else   if (!empty($_GET['id_producto'])) {
        $id_producto=$_GET['id_producto'];
        $api=new Producto();
        $obj=$api->View($id_producto);
        $json=json_encode($obj,JSON_UNESCAPED_UNICODE);
        echo $json;   
    } 
      
}

if($method=="PUT"){    
    $json=null;
    $obj =new  Producto();   
    $data=json_decode(file_get_contents("php://input"),true);  
    $obj->id_producto    =$data['id_producto'];    
    $obj->nombre_producto=$data['nombre_producto'];    
    $obj->id_categoria=$data['id_categoria'];   
    $obj->precio_producto=$data['precio_producto'];    
    $obj->estado=$data['estado'];    
    $obj->cantidad_producto=$data['cantidad_producto'];   
    $obj->descripcion=$data['descripcion'];    
    $count=$obj->Update($obj);

    if ($count>0) {
        $msg = new ArrayObject([], ArrayObject::ARRAY_AS_PROPS);                
        $msg['status'] =  "200";   
        $msg['error'] =  false; 
        $msg['afect'] =  $count; 

    }else{
        $msg = new ArrayObject([], ArrayObject::ARRAY_AS_PROPS);                
        $msg['status'] =  "404";   
        $msg['error'] =  true; 
        $msg['afect'] =  $count; 
    }
    echo json_encode($msg, JSON_UNESCAPED_UNICODE); 
   
      
}






  
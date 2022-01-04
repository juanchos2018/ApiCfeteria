<?php
require_once('../Model/Pedidos.php');
require_once('../Includes/cors.php');

$method=$_SERVER['REQUEST_METHOD'];


if ($method=="POST"){
    $json=null;
    $obj =new  Pedidos();   
    $data=json_decode(file_get_contents("php://input"),true);   

    $obj->fecha_pedido=date("Y-m-d h:i:s");
    $obj->hora_pedido= $data['hora_pedido'];
    $obj->estado_pedido=$data['estado_pedido'];
    $obj->descripcion_pedido="descritiopn";
    $obj->cod_auxiliar=$data['cod_auxiliar'];
    $obj->especialidad=$data['especialidad'];
    $obj->piso_especialidad=$data['piso_especialidad'];
    $obj->des_auxiliar=$data['des_auxiliar'];
    $obj->TotalPedido=$data['TotalPedido'];
    $obj->color=$data['color'];
    $obj->detallePedido=$data['detallePedido'];

    $json=$obj->Store3($obj);
    echo $json;  
}   


if($method=="GET"){     
    if(!empty($_GET['tipo'])){
        $tipo=$_GET['tipo'];
        if ($tipo=="nuevo") {
            $api=new Pedidos();
             $obj=$api->Get();
            $json=json_encode($obj);
            echo $json;   
        }
        else if ($tipo=="terminado"){
            $api=new Pedidos();
            $obj=$api->Gettendidos();
            $json=json_encode($obj);
            echo $json;   
        }
        else if ($tipo=="mio") {
            $cod_auxiliar=$_GET['cod_auxiliar'];
            $api=new Pedidos();
            $obj=$api->GetMiOrder($cod_auxiliar);
            $json=json_encode($obj);
            echo $json;              
        }
    }
         
}

if($method=="PUT"){
     
    $obj=new Pedidos();   
    $data=json_decode(file_get_contents("php://input"),true);  
    $obj->estado_pedido=$data['estado_pedido'];    
    $obj->id_pedido=$data['id_pedido'];    
    $json=$obj->Update($obj);
  
    $msg = new ArrayObject([], ArrayObject::ARRAY_AS_PROPS);                
    $msg['status'] =  "200";   
    $msg['error'] =  false; 
    $msg['resultado'] =  $json; 
    echo json_encode($msg, JSON_UNESCAPED_UNICODE); 
  
    
}



  
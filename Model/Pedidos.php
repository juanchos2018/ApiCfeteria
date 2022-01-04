<?php
date_default_timezone_set('America/Lima');

class Pedidos{

    public  $id_pedido;
    public  $fecha_pedido;        
    public  $hora_pedido;
    public  $estado_pedido;    
    public  $descripcion_pedido;
    public  $cod_auxiliar;   
    public  $especialidad;    
    public  $piso_especialidad;
    public  $detallePedido;
    public  $TotalPedido;
    public  $area;
    public  $color;


    public $conectar;
    public $conexi;


    function __construct(){
        require_once('../Conexion/conexion.php');
        $conex=new Conexion();
        $conex="";
        $this->conexi = new Conexion($conex);
        $this->conectar=$this->conexi->conectar();

	}

    public function Store() {
        $stmt = $this->dbConn->prepare('INSERT INTO PEDIDO_COCINA VALUES(null, :userid, :msg, :createdOn)');
        $stmt->bindParam(':userid', $this->userId);
        $stmt->bindParam(':msg', $this->msg);
        $stmt->bindParam(':createdOn', $this->createdOn);
        
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function Store3($obj){
        $query = "INSERT INTO PEDIDO_COCINA  (fecha_pedido, hora_pedido,estado_pedido,descripcion_pedido,cod_auxiliar,especialidad,piso_especialidad,des_auxiliar,TotalPedido,area,color) 
         VALUES ('$obj->fecha_pedido', '$obj->hora_pedido', '$obj->estado_pedido', '$obj->descripcion_pedido', '$obj->cod_auxiliar', '$obj->especialidad', '$obj->piso_especialidad','$obj->des_auxiliar','$obj->TotalPedido','$obj->area','$obj->color'); SELECT SCOPE_IDENTITY()";
        $resource=sqlsrv_query($this->conectar, $query); 
        sqlsrv_next_result($resource); 
        sqlsrv_fetch($resource); 
        $id_pedido= sqlsrv_get_field($resource, 0); 

        $lista =array();
        foreach ($obj->detallePedido as $item){            
              $fecha=date("Y-m-d h:i:s");
              $this->StoreDetalle($id_pedido,$item['id_categoria'],$item['id_producto'],$item['cantidad_pedido'],$fecha,$item['descripcion']);

        }
        $this->conexi->desconectar();   
        return   $message= '{"resultado":"Registrado"}';

    }

    public function StoreDetalle($id_pedido,$id_categoria,$id_producto,$cantidad_pedido,$fecha_pedido,$descripcion){
        $consulta=("INSERT INTO DETALLE_PEDIDO_COCINA (id_pedido, id_categoria,id_producto,cantidad_pedido,fecha_pedido,descripcion) 
        VALUES('$id_pedido','$id_categoria', '$id_producto', '$cantidad_pedido', '$fecha_pedido', '$descripcion')") or die (sqlsrv_error());       
        $stmt = sqlsrv_prepare($this->conectar, $consulta, array(&$dude, &$time));
        $message="";
        if( sqlsrv_execute($stmt) === false ) {
            die( print_r( sqlsrv_errors(), true));
            $message= '{"resultado":"Error"}';
        }
        else{                 
            $message= '{"resultado":"Registrado"}';
        }     
        return $message;  

    }
    public function Store2($obj){
        $consulta=("INSERT INTO PEDIDO_COCINA (fecha_pedido, hora_pedido,estado_pedido,descripcion_pedido,cod_auxiliar,especialidad,piso_especialidad) 
        VALUES('$obj->fecha_pedido', '$obj->hora_pedido', '$obj->estado_pedido', '$obj->descripcion_pedido', '$obj->cod_auxiliar', '$obj->especialidad', '$obj->piso_especialidad')") or die (sqlsrv_error());       
        $stmt = sqlsrv_prepare($this->conectar, $consulta, array(&$dude, &$time));
        
        $message="";
        if( sqlsrv_execute($stmt) === false ) {
            die( print_r( sqlsrv_errors(), true));
            $message= '{"resultado":"Error"}';
        }
        else{     
            $message= '{"resultado":"Registrado"}';
        }   
        $this->conexi->desconectar();      
        return true; 
    }

    public function Get() {             
        $fecha = date('Y-m-d');
        $sql="SELECT pe.id_pedido,FORMAT (pe.fecha_pedido, 'dd-MM-yyyy') as fecha_pedido,pe.hora_pedido,pe.estado_pedido,pe.cod_auxiliar,mae.des_auxiliar,pe.area,pe.piso_especialidad,pe.color from PEDIDO_COCINA as pe
        inner join MAE_AUXILIAR as mae 
        on pe.cod_auxiliar=mae.COD_AUXILIAR where pe.fecha_pedido='$fecha' and pe.estado_pedido <3 ";       
        $result = sqlsrv_query($this->conectar, $sql);
        $array = array();     
        $i = 0;
        $e=0;
    
        while($rows = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)){  

             $array2 = array();
             $objeto = new ArrayObject([], ArrayObject::ARRAY_AS_PROPS);  
             $objeto['id_pedido']         = $rows['id_pedido'];
             $objeto['fecha_pedido']      = $rows['fecha_pedido'];
             $objeto['hora_pedido']       = $rows['hora_pedido'];
             $objeto['estado_pedido']     = (int)$rows['estado_pedido'];
             $objeto['cod_auxiliar']      = $rows['cod_auxiliar']; 
             $objeto['des_auxiliar']      = $rows['des_auxiliar']; 
             $objeto['area']              = $rows['area'];           
             $objeto['piso_especialidad'] = $rows['piso_especialidad'];
             $objeto['color']              = $rows['color'];

            $id_pedido=$rows['id_pedido'];    

            $detallePedido = sqlsrv_query($this->conectar,"SELECT   det.id_pedido_detalle, det.id_pedido,det.id_categoria,det.id_producto,pro.nombre_producto,det.cantidad_pedido,det.descripcion from DETALLE_PEDIDO_COCINA as det
                        inner join PRODUCTO_COCINA as pro
                        on  det.id_producto = pro.id_producto
                        where det.id_pedido='$id_pedido'");

            while($rows2 = sqlsrv_fetch_array($detallePedido,SQLSRV_FETCH_ASSOC)){    
                $objeto2= new ArrayObject([], ArrayObject::ARRAY_AS_PROPS);  
                $objeto2['id_pedido_detalle'] = $rows2['id_pedido_detalle'];
                $objeto2['id_categoria']    = $rows2['id_categoria'];
                $objeto2['id_producto']     = $rows2['id_producto'];
                $objeto2['nombre_producto'] = $rows2['nombre_producto'];
                $objeto2['cantidad_pedido'] = $rows2['cantidad_pedido'];
                $objeto2['descripcion']     = $rows2['descripcion'];
                array_push($array2,$objeto2); 
                $e++;           
            }
            $objeto['detalle'] = $array2;          
            $i++;
            array_push($array,$objeto); 
        }      
      
        $this->conexi->desconectar();
        return $array;   
     }


     public function GetMiOrder($cod_auxiliar) {             
        $fecha = date('Y-m-d');
        $sql="SELECT pe.id_pedido,FORMAT (pe.fecha_pedido, 'dd-MM-yyyy') as fecha_pedido,pe.hora_pedido,pe.estado_pedido,pe.cod_auxiliar,mae.des_auxiliar,pe.area,pe.piso_especialidad,pe.color from PEDIDO_COCINA as pe
        inner join MAE_AUXILIAR as mae 
        on pe.cod_auxiliar=mae.COD_AUXILIAR where pe.fecha_pedido='$fecha' and pe.cod_auxiliar ='$cod_auxiliar' ";       
        $result = sqlsrv_query($this->conectar, $sql);
        $array = array();     
        $i = 0;
        $e=0;
    
        while($rows = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)){  

             $array2 = array();
             $objeto = new ArrayObject([], ArrayObject::ARRAY_AS_PROPS);  
             $objeto['id_pedido']         = $rows['id_pedido'];
             $objeto['fecha_pedido']      = $rows['fecha_pedido'];
             $objeto['hora_pedido']       = $rows['hora_pedido'];
             $objeto['estado_pedido']     = (int)$rows['estado_pedido'];
             $objeto['cod_auxiliar']      = $rows['cod_auxiliar']; 
             $objeto['des_auxiliar']      = $rows['des_auxiliar']; 
             $objeto['area']              = $rows['area'];           
             $objeto['piso_especialidad'] = $rows['piso_especialidad'];
             $objeto['color']              = $rows['color'];

            $id_pedido=$rows['id_pedido'];    

            $detallePedido = sqlsrv_query($this->conectar,"SELECT   det.id_pedido_detalle, det.id_pedido,det.id_categoria,det.id_producto,pro.nombre_producto,det.cantidad_pedido,det.descripcion from DETALLE_PEDIDO_COCINA as det
                        inner join PRODUCTO_COCINA as pro
                        on  det.id_producto = pro.id_producto
                        where det.id_pedido='$id_pedido'");

            while($rows2 = sqlsrv_fetch_array($detallePedido,SQLSRV_FETCH_ASSOC)){    
                $objeto2= new ArrayObject([], ArrayObject::ARRAY_AS_PROPS);  
                $objeto2['id_pedido_detalle'] = $rows2['id_pedido_detalle'];
                $objeto2['id_categoria']    = $rows2['id_categoria'];
                $objeto2['id_producto']     = $rows2['id_producto'];
                $objeto2['nombre_producto'] = $rows2['nombre_producto'];
                $objeto2['cantidad_pedido'] = $rows2['cantidad_pedido'];
                $objeto2['descripcion']     = $rows2['descripcion'];
                array_push($array2,$objeto2); 
                $e++;           
            }
            $objeto['detalle'] = $array2;          
            $i++;
            array_push($array,$objeto); 
        }      
      
        $this->conexi->desconectar();
        return $array;   
     }

     public function Gettendidos() {       
       
        $fecha = date('Y-m-d');
        $sql="SELECT pe.id_pedido,FORMAT (pe.fecha_pedido, 'dd-MM-yyyy') as fecha_pedido,pe.hora_pedido,pe.estado_pedido,pe.cod_auxiliar,mae.des_auxiliar,pe.area,pe.piso_especialidad,pe.color from PEDIDO_COCINA as pe
        inner join MAE_AUXILIAR as mae 
        on pe.cod_auxiliar=mae.COD_AUXILIAR where pe.fecha_pedido='$fecha' and pe.estado_pedido =3 ";       
        $result = sqlsrv_query($this->conectar, $sql);
        $array = array();     
        $i = 0;
        $e=0;
    
        while($rows = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)){  

             $array2 = array();
             $objeto = new ArrayObject([], ArrayObject::ARRAY_AS_PROPS);  
             $objeto['id_pedido']         = $rows['id_pedido'];
             $objeto['fecha_pedido']      = $rows['fecha_pedido'];
             $objeto['hora_pedido']       = $rows['hora_pedido'];
             $objeto['estado_pedido']     = (int)$rows['estado_pedido'];
             $objeto['cod_auxiliar']      = $rows['cod_auxiliar']; 
             $objeto['des_auxiliar']      = $rows['des_auxiliar']; 
             $objeto['area']              = $rows['area'];           
             $objeto['piso_especialidad'] = $rows['piso_especialidad'];
             $objeto['color']              = $rows['color'];

            $id_pedido=$rows['id_pedido'];    

            $detallePedido = sqlsrv_query($this->conectar,"SELECT   det.id_pedido_detalle, det.id_pedido,det.id_categoria,det.id_producto,pro.nombre_producto,det.cantidad_pedido,det.descripcion from DETALLE_PEDIDO_COCINA as det
                        inner join PRODUCTO_COCINA as pro
                        on  det.id_producto = pro.id_producto
                        where det.id_pedido='$id_pedido'");

            while($rows2 = sqlsrv_fetch_array($detallePedido,SQLSRV_FETCH_ASSOC)){    
                $objeto2= new ArrayObject([], ArrayObject::ARRAY_AS_PROPS);  
                $objeto2['id_pedido_detalle'] = $rows2['id_pedido_detalle'];
                $objeto2['id_categoria']    = $rows2['id_categoria'];
                $objeto2['id_producto']     = $rows2['id_producto'];
                $objeto2['nombre_producto'] = $rows2['nombre_producto'];
                $objeto2['cantidad_pedido'] = $rows2['cantidad_pedido'];
                $objeto2['descripcion']     = $rows2['descripcion'];
                array_push($array2,$objeto2); 
                $e++;           
            }
            $objeto['detalle'] = $array2;          
            $i++;
            array_push($array,$objeto); 
        }      
      
        $this->conexi->desconectar();
        return $array;   
     }

     public function Update($obj){
        $color="bg-positive";
        if ($obj->estado_pedido==1) {
            $color="bg-warning";
        }
        else if ($obj->estado_pedido==2){
            $color="bg-primary";
        }      
        else if ($obj->estado_pedido==3){
            $color="bg-accent";
        }
        $consulta = "UPDATE  PEDIDO_COCINA set estado_pedido='$obj->estado_pedido',color='$color' where id_pedido='$obj->id_pedido'";                   
        $params = array("updated data", 1);
        $stmt = sqlsrv_query($this->conectar, $consulta, $params);
        $rows_affected = sqlsrv_rows_affected($stmt);
        $this->conexi->desconectar();
        if( $rows_affected === false) {
            die( print_r( sqlsrv_errors(), true));
        } elseif( $rows_affected == -1) {
            return "no hay información disponible";
        } else {
            return   $message= 'Registrado';
           // return $rows_affected." las filas se actualizaron";
        }     
     }

     public function Update2($obj){
        $color="bg-positive";
        if ($obj->estado_pedido==1) {
            $color="bg-warning";
        }
        else if ($obj->estado_pedido==2){
            $color="bg-primary";
        }      
        else if ($obj->estado_pedido==3){
            $color="bg-accent";
        }
        $consulta = "UPDATE  PEDIDO_COCINA set estado_pedido='$obj->estado_pedido',color='$color' where id_pedido='$obj->id_pedido'";                   
        $params = array("updated data", 1);
        $stmt = sqlsrv_query($this->conectar, $consulta, $params);
        $rows_affected = sqlsrv_rows_affected($stmt);
        $this->conexi->desconectar();
        if( $rows_affected === false) {
            die( print_r( sqlsrv_errors(), true));
        } elseif( $rows_affected == -1) {
            return "no hay información disponible";
        } else {
            return   true;
           // return $rows_affected." las filas se actualizaron";
        }     
     }


     public function DeleteOrder($obj){
        $color="bg-positive";
        if ($obj->estado_pedido==1) {
            $color="bg-warning";
        }
        else if ($obj->estado_pedido==2){
            $color="bg-primary";
        }      
        else if ($obj->estado_pedido==3){
            $color="bg-accent";
        }
        $consulta = "UPDATE  PEDIDO_COCINA set estado_pedido='$obj->estado_pedido',color='$color' where id_pedido='$obj->id_pedido'";                   
        $params = array("updated data", 1);
        $stmt = sqlsrv_query($this->conectar, $consulta, $params);
        $rows_affected = sqlsrv_rows_affected($stmt);
        $this->conexi->desconectar();
        if( $rows_affected === false) {
            die( print_r( sqlsrv_errors(), true));
        } elseif( $rows_affected == -1) {
            return "no hay información disponible";
        } else {
            return   $message= 'Registrado';
           // return $rows_affected." las filas se actualizaron";
        }     
     }


}

?>
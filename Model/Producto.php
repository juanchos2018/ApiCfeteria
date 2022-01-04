<?php
require_once('../Conexion/conexion.php');

class Producto{

    public  $id_producto;
    public  $nombre_producto;
    public  $descripcion;
    public  $id_categoria;
    public  $precio_producto;
    public  $estado;
    public  $cantidad_producto;
    public  $fecha;
    public  $imagen;

    public $conectar;
    public $conexi;

    function __construct(){
			
        $conex=new Conexion();
        $conex="";
        $this->conexi = new Conexion($conex);
        $this->conectar=$this->conexi->conectar();

	}

    public function Store($obj)
    {            
        $consulta=("INSERT INTO PRODUCTO_COCINA (nombre_producto, id_categoria,precio_producto,estado,cantidad_producto,fecha,imagen,descripcion) 
        VALUES('$obj->nombre_producto', '$obj->id_categoria', '$obj->precio_producto', '$obj->estado', '$obj->cantidad_producto', '$obj->fecha','$obj->imagen','$obj->descripcion')") or die (sqlsrv_error());       
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
        return $message;     
       
    }  

    public function Get($id_categoria) {        

       $sql="SELECT  pro.id_producto,pro.nombre_producto,pro.precio_producto,pro.estado,pro.id_categoria,cat.nombre_categoria,pro.descripcion FROM PRODUCTO_COCINA  as pro 
       inner join CATEGORIA_COCINA as cat
       on pro.id_categoria =cat.id_categoria
       where pro.id_categoria='$id_categoria'";       
       $result = sqlsrv_query($this->conectar, $sql);
       $array = array();
       $i = 0;
       while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)){       
            $array[$i]['id_producto']        = (int)$row['id_producto'];
            $array[$i]['id_categoria']       = (int)$row['id_categoria'];
            $array[$i]['nombre_producto']    = utf8_encode($row['nombre_producto']);
            $array[$i]['descripcion']        = utf8_encode($row['descripcion']);
            $array[$i]['precio_producto']    = $row['precio_producto'];  
            $array[$i]['estado']             = $row['estado'];
            $array[$i]['nombre_categoria']   = utf8_encode($row['nombre_categoria']);         
            $i++;  
       }
       $this->conexi->desconectar();
       return $array;   
    }

    public function View($id_producto) {        

        $sql="SELECT pro.id_producto,pro.nombre_producto,pro.descripcion,pro.precio_producto,pro.estado,pro.imagen,pro.id_categoria,ca.nombre_categoria,pro.cantidad_producto from PRODUCTO_COCINA as pro
        inner join CATEGORIA_COCINA as ca
        on pro.id_categoria =ca.id_categoria
        where pro.id_producto='$id_producto'";       
         $params = array();
         $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET );
         $stmt = sqlsrv_query($this->conectar, $sql , $params, $options ); 
         $row_count = sqlsrv_num_rows($stmt);
         $obj = new ArrayObject([], ArrayObject::ARRAY_AS_PROPS);    
         if ($row_count>0) {                    
             if( sqlsrv_fetch( $stmt ) === false) {
                 die( print_r( sqlsrv_errors(), true));         
             }       
             $obj = new ArrayObject([], ArrayObject::ARRAY_AS_PROPS);                
             $obj['id_producto']         = sqlsrv_get_field( $stmt, 0);   
             $obj['nombre_producto']     = sqlsrv_get_field( $stmt, 1);          
             $obj['descripcion']         = sqlsrv_get_field( $stmt, 2);              
             $obj['precio_producto']     = sqlsrv_get_field( $stmt, 3);  
             $obj['estado']              = sqlsrv_get_field( $stmt, 4);  
             $obj['imagen']              = sqlsrv_get_field( $stmt, 5);  
             $obj['id_categoria']        = sqlsrv_get_field( $stmt, 6);  
             $obj['nombre_categoria']    = sqlsrv_get_field( $stmt, 7);  
             $obj['cantidad_producto']   = sqlsrv_get_field( $stmt, 8);  
             $obj['EXISTE'] = "Si";  
             return $obj;   
         }else{
             $obj['EXISTE'] = "No";  
             return $obj;   
         }
     }

     public function Update($obj){       

        $consulta = "UPDATE  PRODUCTO_COCINA set nombre_producto='$obj->nombre_producto', descripcion='$obj->descripcion', precio_producto='$obj->precio_producto', estado='$obj->estado'  where id_producto='$obj->id_producto'";                   
        $params = array("updated data", 1);
        $stmt = sqlsrv_query($this->conectar, $consulta, $params);
        $rows_affected = sqlsrv_rows_affected($stmt);
        $this->conexi->desconectar();
        if( $rows_affected === false) {
            die( print_r( sqlsrv_errors(), true));
        } elseif( $rows_affected == -1) {
            return "no hay información disponible";
        } else {
            return $rows_affected;
        }            
    }  
}

?>
<?php
require_once('../Conexion/conexion.php');

class Categoria{

    public  $id_categoria;
    public  $nombre_categoria;
    public  $estado;

    public $conectar;
    public $conexi;

    function __construct(){
			
        $conex=new Conexion();
        $conex="";
        $this->conexi = new Conexion($conex);
        $this->conectar=$this->conexi->conectar();

	}

    public function probate($obj){
            $query = "INSERT INTO CATEGORIA_COCINA  (nombre_categoria, estado)  VALUES ('$obj->nombre_categoria', '$obj->estado'); SELECT SCOPE_IDENTITY()";
            $resource=sqlsrv_query($this->conectar, $query); 
            sqlsrv_next_result($resource); 
            sqlsrv_fetch($resource); 
            $id= sqlsrv_get_field($resource, 0); 

            return "{'id':'$id'}";
    }

    public function Store($obj){        

        $sql = "SELECT * FROM  CATEGORIA_COCINA where nombre_categoria='$obj->nombre_categoria'";
        $params = array();
        $options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
        $stmt = sqlsrv_query($this->conectar, $sql , $params, $options );
        $row_count = sqlsrv_num_rows($stmt);
     
        if ($row_count>0){
            return '{"resultado":"Existe"}';
        }        
        else{
            $consulta=("INSERT INTO CATEGORIA_COCINA (nombre_categoria, estado) 
            VALUES('$obj->nombre_categoria', '$obj->estado')") or die (sqlsrv_error());       
            $stmt = sqlsrv_prepare($this->conectar, $consulta, array(&$dude, &$time));
            if( sqlsrv_execute($stmt) === false ) {
                die( print_r( sqlsrv_errors(), true));
            }
            else{              
                return '{"resultado":"Registrado"}';
            }         
        }  
        $this->conexi->desconectar();
       
    }  

    public function Get() {        

       $sql="SELECT  id_categoria,nombre_categoria,estado FROM CATEGORIA_COCINA";       
       $result = sqlsrv_query($this->conectar, $sql);
       $array = array();
       $i = 0;
       while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)){        
            $array[$i]['id_categoria'] = (int)$row['id_categoria'];
            $array[$i]['nombre_categoria'] = utf8_encode($row['nombre_categoria']);
            $array[$i]['estado'] = $row['estado'];           
            $i++;
       }
       $this->conexi->desconectar();
       return $array;   
    }

    public function Update($obj){       

        $consulta = "UPDATE  CATEGORIA_COCINA set nombre_categoria='$obj->nombre_categoria', estado='$obj->estado' where id_categoria='$obj->id_categoria'";                   
        $params = array("updated data", 1);
        $stmt = sqlsrv_query($this->conectar, $consulta, $params);
        $rows_affected = sqlsrv_rows_affected($stmt);
        $this->conexi->desconectar();
        if( $rows_affected === false) {
            die( print_r( sqlsrv_errors(), true));
        } elseif( $rows_affected == -1) {
            return "no hay información disponible";
        } else {
            return $rows_affected." las filas se actualizaron";
        }            
    }  
  

    public function Update2($obj){       

        $consulta = "UPDATE  CATEGORIA_COCINA set nombre_categoria='$obj->nombre_categoria', estado='$obj->estado' where id_categoria='$obj->id_categoria'";                   
        $stmt = sqlsrv_prepare($this->conectar, $consulta, array(&$dude, &$time));    
        if( sqlsrv_execute($stmt) === false ) {
            die( print_r( sqlsrv_errors(), true));
            return 'Error';
        }
        else{      
            return 'Editado';
        }   
        $this->conexi->desconectar();
    }  

 

}

?>
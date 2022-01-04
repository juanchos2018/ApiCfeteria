<?php
date_default_timezone_set('America/Lima');

class Login{

    public  $COD_USUARIO;
    public  $DES_PASSWORD;        
   
    public $conectar;
    public $conexi;


    function __construct(){
        require_once('../Conexion/conexion.php');
        $conex=new Conexion();
        $conex="";
        $this->conexi = new Conexion($conex);
        $this->conectar=$this->conexi->conectar();
	}

    public function Ingresar($obj) {
        $sql = "SELECT COD_USUARIO, DES_PASSWORD  FROM  MAE_USUARIO where COD_USUARIO ='$obj->COD_USUARIO'" ;
        $params = array();
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET );
        $stmt = sqlsrv_query($this->conectar, $sql , $params, $options ); 
        $row_count = sqlsrv_num_rows($stmt);
        if ($row_count>0) {                    
            if( sqlsrv_fetch( $stmt ) === false) {
                die( print_r( sqlsrv_errors(), true));         
            }      
            $DES_PASSWORD = sqlsrv_get_field( $stmt, 1);                
            if ($obj->DES_PASSWORD==$this->Decifrar($DES_PASSWORD)) {      
                
                $objDatos = $this->DatosUsuario($obj->COD_USUARIO);
                return $objDatos;
                // if ($objDato->EXISTE=="Si") {
                //  return $objDatos;
                // }else{
                //     return "SinDatos";
                // }
               
            }else{
                return "Error en su clave";
            }           
        }else{
            return "No existe";   
        }
    }

    public function DatosUsuario($cod_auxiliar){

        $sql = "SELECT cv.COD_MEDICO,cv.COD_AUXILIAR,mae.DES_AUXILIAR FROM CVE_MEDICO as cv
        INNER JOIN MAE_AUXILIAR  as mae
        on cv.COD_AUXILIAR=mae.COD_AUXILIAR
        where cv.COD_USUARIO_MEDICO ='$cod_auxiliar'" ;
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
            $obj['COD_MEDICO']   = sqlsrv_get_field( $stmt, 0);   
            $obj['COD_AUXILIAR'] = sqlsrv_get_field( $stmt, 1);          
            $obj['DES_AUXILIAR'] =sqlsrv_get_field( $stmt, 2);              
            $obj['EXISTE'] = "Si";  
            return $obj;
            
                    
        }else{
            $obj['EXISTE'] = "No";  
            return $obj;   
        }
    }

    public function Decifrar($DES_PASSWORD){   
           //descencriptar la contraseña del usuario       
           $il_longi = 0;
           $il_count = 0;
           $il_suma = 0;
           $il_base = 0;    
           $vl_cadena_conv = "";
           $as_cadena_dev = "";
           $as_cadena_ing = $DES_PASSWORD;

           $il_base = ord(substr($as_cadena_ing, -1))/2;
           $vl_cadena_conv = substr($as_cadena_ing,1,(strlen($as_cadena_ing)-2));
           $il_longi = round((strlen($vl_cadena_conv)/4),0);
           $vl_cadena_conv = substr($vl_cadena_conv, $il_longi, strlen($vl_cadena_conv) - (2*$il_longi));

           $il_longi = strlen($vl_cadena_conv);
           $il_count = 0;
           $il_suma = 0;
           while($il_count < $il_longi){
               $as_cadena_dev = $as_cadena_dev.chr(ord(substr($vl_cadena_conv, $il_count, 1)) - $il_base);
               $il_count++;
           }
           return $as_cadena_dev;       
          
   }

    public function Ingresar2($obj) {
        
        $sql = "SELECT COD_USUARIO, DES_PASSWORD  FROM  MAE_USUARIO where COD_USUARIO='$obj->COD_USUARIO' ";

     
        $params = array();
        $options = array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
        $stmt = sqlsrv_query($this->conectar, $sql , $params, $options );
        $row_count = sqlsrv_num_rows($stmt);

        if( sqlsrv_fetch( $stmt ) === false) {
            die( print_r( sqlsrv_errors(), true));         
        }      
        $DES_PASSWORD = sqlsrv_get_field( $stmt, 1);
        return $DES_PASSWORD;
    }
   
  
  

  

  

  
  


}

?>
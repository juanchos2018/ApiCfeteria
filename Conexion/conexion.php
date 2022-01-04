<?php

class conexion {

    var $user;
	var $pass;
	var $host;
	var $database;
	var $conn;
 
    function __construct($parametros=""){
		if($parametros==""){	
			
			$user="sa";			
			$pass="123456";
			$database="Cafeteria";		
            $serverName = "localhost";

		}else{

			$arrayParametros = explode(",",$parametros);
			$user = trim($arrayParametros[0]);
			$pass = trim($arrayParametros[1]);
			$database = trim($arrayParametros[2]);
			$serverName = trim($arrayParametros[3]);
		}			

		$this->user = $user;
		$this->pass = $pass;
		$this->serverName = $serverName;
		$this->database = $database;	
	}

    function conectar(){
		try {
           
            $connectionInfo = array("Database"=>$this->database, "UID"=>$this->user, "PWD"=>$this->pass);
            $this->conn = sqlsrv_connect($this->serverName, $connectionInfo);
            return $this->conn;
     
        } catch (PDOException $e){
		    echo $e->getMessage();
		}
	}
    function desconectar(){		
        sqlsrv_close( $this->conn );
	}

}

?>
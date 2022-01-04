<?php
namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
//require "../db/users.php";
//require "../db/chatrooms.php";
require "../Model/Pedidos.php";
date_default_timezone_set('America/Lima');
class Pedido implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        echo "servidor Iniciado.";
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "Nueva Conexion! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        // echo sprintf('Connection %d  Recbido "%s" to %d other connection%s' . "\n"
        //     , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');
        echo "Data Recibida";
        $data = json_decode($msg, true);
        $obj = new \Pedidos;
        if ($data['tipo']=="Store") {
            echo "tipo Store";
            $obj->fecha_pedido=date("Y-m-d");
            $obj->hora_pedido= $data['hora_pedido'];
            $obj->estado_pedido=$data['estado_pedido'];
            $obj->descripcion_pedido="descritiopn";
            $obj->cod_auxiliar=$data['cod_auxiliar'];
            $obj->especialidad=$data['especialidad'];
            $obj->piso_especialidad=$data['piso_especialidad'];
            $obj->des_auxiliar=$data['des_auxiliar'];
            $obj->TotalPedido=$data['TotalPedido'];
            $obj->area=$data['area'];
            $obj->color=$data['color'];
            $obj->detallePedido=$data['detallePedido'];
    
            if($obj->Store3($obj)) {         
                $data['from'] = "juancho";
                $data['msg']  = "mensaje";
                $data['dt']  = date("d-m-Y h:i:s");
            }
    
            foreach ($this->clients as $client) {
                if ($from == $client) {
                    $data['from']  = "Me";
                } else {
                    $data['from']  = "1";
                }
                $client->send(json_encode($data));
            }
        }
        else if ($data['tipo']=="Update") {
            echo "tipo Update";
            $obj->estado_pedido=$data['estado_pedido'];    
            $obj->id_pedido=$data['id_pedido'];    
           // $json=$obj->Update($obj);

            if($obj->Update2($obj)) {         
                $data['from'] = "juancho";
                $data['msg']  = "mensaje";
                $data['dt']  = date("d-m-Y h:i:s");
            }
    
            foreach ($this->clients as $client) {
                if ($from == $client) {
                    $data['from']  = "Me";
                } else {
                    $data['from']  = "1";
                }
                $client->send(json_encode($data));
            }

        }
       
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        echo "Esta Conexion {$conn->resourceId} ha sido DESCONECTADO\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}
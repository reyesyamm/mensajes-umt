<?php       
header('Content-Type: application/json');
date_default_timezone_set("America/Merida");

require_once 'Database.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    // leemos los datos
    // 1. primero de una locacion especial de php
    $datosEnJsonString = file_get_contents("php://input");

    // 2. Convertimos a un array asociativo
    $datos = json_decode($datosEnJsonString);

    if (
            isset($datos['id_usuario']) &&
            isset($datos['id_mensaje'])
    ) {
        
        // primero obtenemos el mensaje
        $sqlSelect = "select * from mensaje where id_mensaje=? and id_usuario=? limit 1;";
        $argumentos = [$datos['id_usuario'], $datos['id_mensaje']];
        
        $sentenciaSql = Database::getInstance()->getDb()->prepare($sqlSelect);
        $res = $sentenciaSql->execute($argumentos);
        
        if($res){
            $mensajeDB = $sentenciaSql->fetch(PDO::FETCH_ASSOC);
            if($mensajeDB){
                
               $sqlDelete = "delete from mensaje where id_mensaje=? and id_usuario=?;";
               $mensaje = "";
               try{
                   $sentenciaSqlDelete = $db->prepare($sqlDelete);
                   $estado = $sentenciaSqlDelete->execute($argumentos);
               } catch (Exception $ex) {
                   $estado = false;
                   $mensaje = $ex->getMessage();
               }
               
               $respuestaFinal = [
                   'estado' => $estado,
                   'mensaje' => $mensaje,
                   'datos' => $mensajeDB
               ];
               
               print json_encode($respuestaFinal);
               
            }else{
                $respuestaIncorrecta = [
                    'estado' => false,
                    'mensaje' => 'El mensaje solicitado no fue encontrado'
                ];

                print json_encode($respuestaIncorrecta);
            }
        }else{
            $respuestaIncorrecta = [
                'estado' => false,
                'mensaje' => 'El mensaje solicitado no fue encontrado'
            ];

            print json_encode($respuestaIncorrecta);
        }
    }else{
        $respuestaIncorrecta = [
            'estado' => false,
            'mensaje' => 'Los datos para eliminar el mensaje no estan completos'
        ];

        print json_encode($respuestaIncorrecta);
    }
}else{
    $respuestaIncorrecta = [
        'estado' => false,
        'mensaje' => 'Tu solicitud no es correcta'
    ];

    print json_encode($respuestaIncorrecta);
}
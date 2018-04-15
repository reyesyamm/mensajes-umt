<?php
header('Content-Type: application/json');
date_default_timezone_set('America/Merida');

require_once 'Database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // leemos los datos
    // 1. primero de una locacion especial de php
    $datosEnJsonString = file_get_contents("php://input");

    // 2. Convertimos a un array asociativo
    $datos = json_decode($datosEnJsonString);

    if (
            isset($datos['titulo']) &&
            isset($datos['contenido']) &&
            isset($datos['id_usuario'])
    ) {

        $fecha = date('Y-m-d H:i:s');

        $sql = "insert into mensaje (titulo, contenido, id_usuario, fecha_creacion, fecha_actualizacion) values (?,?,?,?,?);";
        $argumentos = [$datos['titulo'], $datos['contenido'], $datos['id_usuario'], $fecha, $fecha];

        $estado = false;
        $mensaje = "";
        
        try {
            $db = Database::getInstance()->getDb();
            $objetoSql = $db->prepare($sql);
            $resultado = $objetoSql->execute($argumentos);
            
            if($resultado){
                $estado = true;
                $mensaje = "Mensaje creado exitosamente";
                $datos['fecha_creacion'] = $fecha;
                $datos['fecha_actualizacion'] = $fecha;
                $datos['id_mensaje'] = $db->lastInsertId();
            }else{
                $mensaje = "Ocurrió un error al crear el mensaje";
            }
            
        } catch (Exception $ex) {
            $mensaje= $ex->getMessage();
        }
        
        
        $respuestaFinal = [
            'estado' => $estado,
            'mensaje' => $mensaje,
            'datos' => $estado ? $datos : null
        ];
        
        print json_encode($respuestaFinal);
        
    } else {
        $respuestaDatosIncompletos = [
            'estado' => false,
            'mensaje' => 'Los datos no estan completos'
        ];

        print json_encode($respuestaDatosIncompletos);
    }
} else {
    $respuestaIncorrecta = [
        'estado' => false,
        'mensaje' => 'Tu solicitud no es correcta'
    ];

    print json_encode($respuestaIncorrecta);
}
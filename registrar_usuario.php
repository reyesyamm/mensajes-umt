<?php
header('Content-Type: application/json');
date_default_timezone_set('America/Merida');

require_once 'Database.php';

// verificamos que el tipo de solicitud sea el correcto
if($_SERVER['REQUEST_METHOD'] == 'POST'){

	// leemos los datos
	// 1. primero de una locacion especial de php
	$datosEnJsonString = file_get_contents("php://input");

	// 2. Convertimos a un array asociativo
	$datos = json_decode($datosEnJsonString);

	// verificamos que los campos obligatorios existan
	if(
		isset($datos['nombre']) &&
		isset($datos['correo']) && 
		isset($datos['genero']) &&
		isset($datos['contrasenia'])
		){

		$fechaHoy = date('Y-m-d H:i:s');


		// creamos la sentencia sql
		$sentenciaSql = "insert into usuario (nombre,correo,genero,contrasenia,fecha_creacion,fecha_actualizacion) values (?,?,?,?,?,?);";

		// deben ir en el mismo orden que en la sentencia sql
		$argumentosSql = [
			$datos['nombre'],
			$datos['correo'],
			$datos['genero'],
			md5($datos['contrasenia']), // md5 no deberia utilizarse para guardar contraseñas, pero para fines didacticos se utilizará en esta ocasión
			$datos['fecha_creacion'],
			$datos['fecha_actualizacion']
		];

		$estado = false;
		$mensaje = "";
		try{

			$db = Database::getInstance()->getDb();
			$objetoSql = $db->prepare($sentenciaSql);
			$resultado = $objetoSql->execute($argumentosSql);

			if($resultado){
				$estado = true;
				$mensaje = "Usuario creado correctamente";
			}

		}catch(Exception $ex){
			$mensaje = $ex->getMessage();
		}
		

		$respuestaFinal = [
			'estado' => $estado,
			'mensaje' => $mensaje,
			'datos' => $estado ? $datos : []
		];


		print json_encode($respuestaFinal);

	}else{
		$respuestaDatosIncompletos = [
			'estado' => false,
			'mensaje' => 'Los datos no estan completos'
		];

		print json_encode($respuestaDatosIncompletos);
	}
}else{
	// respondemos con un estado false
	$respuestaIncorrecta = [
		'estado' => false,
		'mensaje' => 'Tu solicitud no es correcta'
	];

	print json_encode($respuestaIncorrecta);
}
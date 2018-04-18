<?php
header('Content-Type: application/json');
date_default_timezone_set('America/Merida');

require_once 'Database.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){

	// leemos los datos
	// 1. primero de una locacion especial de php
	$datosEnJsonString = file_get_contents("php://input");

	// 2. Convertimos a un array asociativo
	$datos = json_decode($datosEnJsonString, true);

	// verificamos que los campos obligatorios existan
	if(
		isset($datos['correo']) && 
		isset($datos['contrasenia'])
		){

		$sentenciaSql = "select * from usuario where correo = ? limit 1;";
		$argumentosSql = [ $datos['correo'] ];

		$db = Database::getInstance()->getDb();

		$objetoSql = $db->prepare($sentenciaSql);

		$resultado = $objetoSql->execute($argumentosSql);

		if($resultado){
			$usuario = $objetoSql->fetch(PDO::FETCH_ASSOC);

			if(count($usuario)>0 && $usuario['contrasenia'] == md5($datos['contrasenia'])){


				$respuestaDatos = [
					'estado' => true,
					'mensaje' => 'Bienvenido',
					'datos' => $usuario
				];

				print json_encode($respuestaDatos);



			}else{
				$respuestaDatosIncompletos = [
					'estado' => false,
					'mensaje' => 'Las credenciales son incorrectas'
				];

				print json_encode($respuestaDatosIncompletos);
			}

		}else{
			$respuestaDatosIncompletos = [
				'estado' => false,
				'mensaje' => 'Ocurrió un error desconocido. Reintenta otra vez'
			];

			print json_encode($respuestaDatosIncompletos);
		}
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
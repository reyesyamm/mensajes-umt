<?php
header('Content-Type: application/json');
date_default_timezone_set('America/Merida');

require_once 'Database.php';

if($_SERVER['REQUEST_METHOD'] == 'GET'){
	
	$db = Database::getInstance()->getDb();

	$consultaSql = "select m.*, u.nombre from mensaje m inner join usuario u on m.id_usuario = u.id_usuario order by m.fecha_actualizacion desc;";
	$objetoConsulta = $db->prepare($consultaSql);
	$resultadoConsultado = $objetoConsulta->execute();

	if($resultadoConsultado){
		$listadoMensajes = $objetoConsulta->fetchAll(PDO::FETCH_ASSOC);

		$respuestaCorrecta = [
			'estado' => true,
			'mensaje' => 'Lista de mensajes obtenida',
			'datos' => $listadoMensajes
		];

		print json_encode($respuestaCorrecta);

	}else{
		$respuestaIncorrecta = [
			'estado' => false,
			'mensaje' => 'Ocurrió un error al leer los datos'
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


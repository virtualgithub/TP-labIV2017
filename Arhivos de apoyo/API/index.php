<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once "materia.php";
require 'vendor/autoload.php';

$app = new \Slim\App(['settings' => ['displayErrorDetails' => true]]);

$app->add(function (Request $request, Response $response, $next) {
    $response = $next($request, $response);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')//servidor
			//->withHeader('Access-Control-Allow-Origin', 'http://localhost:8100')//local
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

$app->post('/materia/GuardarDispositivo', function (Request $request, Response $response) {
	return $response->withJson(Materia::GuardarDispositivo($request->getParam('idUsuario'), $request->getParam('plataforma'), $request->getParam('version'), $request->getParam('fabricante'), $request->getParam('modelo')));
});

$app->get('/materia/TraerAlumnoAsistenciaSegunMateriaAlumnoDetalle', function (Request $request, Response $response){
    return $response->withJson(Materia::TraerAlumnoAsistenciaSegunMateriaAlumnoDetalle($request->getParam('idMateria'), $request->getParam('idAlumno')));
});

$app->get('/materia/TraerAlumnoAsistenciaSegunMateriaAlumnoResumen', function (Request $request, Response $response){
    return $response->withJson(Materia::TraerAlumnoAsistenciaSegunMateriaAlumnoResumen($request->getParam('idMateria'), $request->getParam('idAlumno')));
});

$app->get('/materia/TraerAlumnoAsistenciaSegunFechaMateriaAlumno', function (Request $request, Response $response){
    return $response->withJson(Materia::TraerAlumnoAsistenciaSegunFechaMateriaAlumno($request->getParam('fecha'), $request->getParam('idMateria'), $request->getParam('idAlumno')));
});

$app->get('/materia/TraerMateriasSegunDiaYAlumno', function (Request $request, Response $response){
    return $response->withJson(Materia::TraerMateriasSegunDiaYAlumno($request->getParam('idDia'), $request->getParam('idAlumno')));
});

$app->get('/materia/TraerAlumnoSegunId', function (Request $request, Response $response){
    return $response->withJson(Materia::TraerAlumnoSegunId($request->getParam('idAlumno')));
});

$app->get('/materia/TraerAlumnosSegunDia', function (Request $request, Response $response){
    return $response->withJson(Materia::TraerAlumnosSegunDia($request->getParam('idDia')));
});

$app->get('/materia/TraerMateriasSegunDiaYAula', function (Request $request, Response $response){
    return $response->withJson(Materia::TraerMateriasSegunDiaYAula($request->getParam('idDia'), $request->getParam('idAula')));
});

$app->get('/materia/TraerAulaSegunId', function (Request $request, Response $response){
    return $response->withJson(Materia::TraerAulaSegunId($request->getParam('idAula')));
});

$app->get('/materia/TraerAulasSegunDia', function (Request $request, Response $response){
    return $response->withJson(Materia::TraerAulasSegunDia($request->getParam('idDia')));
});

$app->get('/materia/TraerMateriasSegunDiaYProfesor', function (Request $request, Response $response){
    return $response->withJson(Materia::TraerMateriasSegunDiaYProfesor($request->getParam('idDia'), $request->getParam('idProfesor')));
});

$app->get('/materia/TraerProfesorSegunId', function (Request $request, Response $response){
    return $response->withJson(Materia::TraerProfesorSegunId($request->getParam('idProfesor')));
});

$app->get('/materia/TraerMateriasSegunIdProfesor', function (Request $request, Response $response){
    return $response->withJson(Materia::TraerMateriasSegunIdProfesor($request->getParam('idProfesor')));
});

$app->get('/materia/TraerProfesoresSegunDia', function (Request $request, Response $response){
    return $response->withJson(Materia::TraerProfesoresSegunDia($request->getParam('idDia'), $request->getParam('idMateria')));
});

$app->get('/materia/TraerAlumnosAsistenciaSegunFechaMateria', function (Request $request, Response $response){
    return $response->withJson(Materia::TraerAlumnosAsistenciaSegunFechaMateria($request->getParam('fecha'), $request->getParam('idMateria')));
});

$app->get('/materia/TraerMateriaSegunIdMateria', function (Request $request, Response $response){
    return $response->withJson(Materia::TraerMateriaSegunIdMateria($request->getParam('idMateria')));
});

$app->get('/materia/TraerMateriasSegunDia', function (Request $request, Response $response){
    return $response->withJson(Materia::TraerMateriasSegunDia($request->getParam('idDia')));
});

$app->get('/materia/TraerAlumnosSegunMateria', function (Request $request, Response $response){
    return $response->withJson(Materia::TraerAlumnosSegunMateria($request->getParam('idMateria')));
});

$app->post('/materia/GuardarAsistencia', function (Request $request, Response $response) {
    $fecha = $request->getParam('fecha');
	$idMateria = $request->getParam('idMateria');
	$datos = $request->getParam('datos');
	foreach ($datos as $idAlumno => $asistencia) {
		Materia::GuardarAsistencia($fecha, $idMateria, $idAlumno, $asistencia);
	}
	return $response->withJson("Exito");
});	



$app->get('/persona/obtenerTodas', function (Request $request, Response $response){
    return $response->withJson(Persona::TraerTodasLasPersonas());
});

$app->delete('/persona/borrar', function (Request $request, Response $response) {
	//BORRA FOTO
	unlink ('fotos/' . $request->getParam('dni') . '.png');
	//BORRA PERSONA
	Persona::BorrarPersona($request->getParam('id'));
    return $response;
});

$app->post('/persona/agregar', function (Request $request, Response $response) {
    //DECODIFICACION DE DATOS DE FORMULARIO Y ALMACENAMIENTO EN ARRAY ASOCIATIVO
	$datosForm = $request->getParsedBody();
    $mensajeError = null;
	//VALIDACION DEL TAMAÑO DE LA IMAGEN
	if ($_FILES['foto']['size'] > (1 /*1MB*/ * 1024 * 1024)) {
		$mensajeError = 'Cambie la imagen, solo se permiten tamaños imagenes de tamaño inferior a 1 MB';
	}
	//VALIDACION DE TIPO DE IMAGEN MEDIANTE EL INTENTO DE PROCESARLA COMO IMAGEN, SI IMAGENINICIAL ES FALSE, FALLO LA VALIDACION
	else if(!($imagenInicial = imagecreatefromstring(file_get_contents($_FILES['foto']['tmp_name'])))) {
		$mensajeError = 'Cambie la imagen, sólo se permiten imágenes con extensión .jpg .jpeg .bmp .gif o .png';
	}
	//CREACION DE PERSONA CON FOTO
	else if(Persona::InsertarPersona(new Persona($datosForm['nombre'], $datosForm['apellido'], $datosForm['dni'], $datosForm['sexo'], $datosForm['pass']))){
		//OBTENCION DE LAS DIMENSIONES DE LA IMAGEN INICIAL
		$imagenInicialAncho = imagesx($imagenInicial);
		$imagenInicialAlto = imagesy($imagenInicial);
		//CREACION DE UNA IMAGEN VACIA CON LAS DIMENSIONES DE LA IMAGEN INCIAL
		$imagenFinal = imagecreatetruecolor($imagenInicialAncho, $imagenInicialAlto);
		//COPIA DE LA IMAGEN INCIAL EN LA FINAL
		imagecopy($imagenFinal, $imagenInicial, 0, 0, 0, 0, $imagenInicialAncho, $imagenInicialAlto);
		//LIBERACION DE LA MEMORIA DE LA IMAGEN INICIAL
		imagedestroy($imagenInicial);
		//GUARDADO DEFINITIVO DE LA IMAGEN EN EL SERVIDOR CONVIRTIENDOLA EN FORMATO PNG
		imagepng($imagenFinal, 'fotos/' . $datosForm['dni'] . '.png');
		//LIBERACION DE LA MEMORIA DE LA IMAGEN FINAL
		imagedestroy($imagenFinal);
	}
	//MENSAJE POR USUARIO DUPLICADO
	else{
		$mensajeError = 'La persona ya existía previamente en la base de datos';
	}
	//CODIFICACION DEL MENSAJE DE ERROR
	return $response->withJson($mensajeError);
});	

$app->run();
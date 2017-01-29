<?php
include('ImageResize.php');
use \Eventviva\ImageResize;
//error_reporting(0); //Activar en producción

//Obtenemos el nombre del archivo
$nombreImagen = $_FILES['imagen-subida']['name'];
//Limpiamos el nombre 
$nombreImagen = htmlspecialchars($nombreImagen);

//Extensiones permitidas
$extensiones = array('jpg', 'gif', 'png');

//Obtenemos la extensión del archivo subido
$extension = explode('.', $nombreImagen);
$extension = end($extension);
$extension = strtolower($extension);

//Si la extensión no está dentro del array,
//no permitimos que se suba
//IMPORTANTE enviar un header de error (en este caso, 500)
if(!in_array($extension, $extensiones)) {
	header('HTTP/1.1 500 Internal Server Error');
	header('Content-Type: application/json');
	$error = array('estado' => 'error', 'mensaje' => 'Formato de imagen no válido en "'.$nombreImagen.'".');
	die(json_encode($error, JSON_FORCE_OBJECT));
}

/* ------------------- */

//Obtenemos el peso del archivo
$pesoImagen = $_FILES['imagen-subida']['size'];
//Definimos el máximo de bytes que debe permitir
$tamañoMaximoBytes = 1572864; // -> 1,5 MB

//Mostramos un mensaje si es mayor al tamaño expresado en Bytes
if($pesoImagen > $tamañoMaximoBytes) {
	header('HTTP/1.1 500 Internal Server Error');
	header('Content-Type: application/json');
	$error = array('estado' => 'error', 'mensaje' => 'El peso máximo permitido es de 1,5 MB. "'.$nombreImagen.'" pesa demasiado.');
	die(json_encode($error, JSON_FORCE_OBJECT));
}

/* ------------------- */

//Si todo está bien, movemos el archivo a "uploads"
$carpetaUploads = dirname(__FILE__)."/uploads/";

$archivoTemporal = $_FILES["imagen-subida"]["tmp_name"];
$targetFile =  $carpetaUploads.$nombreImagen;

if (move_uploaded_file($archivoTemporal,$targetFile)) {
	//Crear thumbnail
	$image = new ImageResize($targetFile);
	$image->resizeToWidth(180); //Ancho de la imagen, se escalará proporcionalmente
	
	if ($extension == "jpg") {
		$image->quality_jpg = 100; //La mejor calidad
	} else if ($extension == "png") {
		$image->quality_png = 9; //La mejor compresión
	}
	
	//Guardar miniatura en la carpeta correspondiente
	$image->save($carpetaUploads."thumbnails/".$nombreImagen);
	
	header("HTTP/1.1 200 OK");
	header('Content-Type: application/json');
	$datos = array('estado' => '200');
	die(json_encode($datos, JSON_FORCE_OBJECT));
} else {
	header('HTTP/1.1 500 Internal Server Error');
	header('Content-Type: application/json');
	$error = array('estado' => 'error', 'mensaje' => 'Error del servidor moviendo el archivo.');
	die(json_encode($error, JSON_FORCE_OBJECT));
}
?>
<?php
require_once('../../../../../receptionProduct/src/archivos.php');
$archivo = new ArchivoTm();
$archivo->setNombreArchivo('../../../../../receptionProduct/data/cambio_precio.txt');
$archivo_cambio_precio = $archivo->leerArchivo();
$result = array();
$estado = '';
$msg = '';
$id_producto = $_POST['id_producto'];
if($archivo_cambio_precio){
	$productos = json_decode($archivo_cambio_precio);
	$total_productos = count($productos);
	for($x=0;$x<$total_productos;$x++){
		if($productos[$x]->productId === $id_producto){
			array_splice($productos,$x,1);
			break;
		}
	}
	$archivo->setContenido(json_encode($productos));
	$archivo->escribir();
	$estado = 'success';
	$msg = 'Producto descartado';
}else{
	$estado = 'error';
	$msg = 'Error al descartar';
}
$result['msg'] = $msg;
$result['estado'] = $estado;
echo json_encode($result);
?>
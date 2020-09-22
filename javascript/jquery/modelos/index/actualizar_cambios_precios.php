<?php
require_once('../../../../../receptionProduct/src/archivos.php');
require_once('../../../../../receptionProduct/src/connectwoo.php');
$archivo = new ArchivoTm();
$cw = new Connectwoo();
$archivo->setNombreArchivo('../../../../../receptionProduct/data/cambio_precio.txt');
$archivo_cambio_precio = $archivo->leerArchivo();
$result = array();
$estado = '';
$msg = '';
$id_producto = $_POST['id_producto'];
$id_producto_woo = $_POST['id_producto_woo'];
$precio = $_POST['precio'];
$precio_competencia = $_POST['precio_competencia'];
if($archivo_cambio_precio){
	$data = [
		'regular_price' => $precio_competencia,
	    'sale_price' => $precio
	];
	$cw->setProduct($id_producto_woo,$data);
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
	$msg = 'Precio actualizado';
}else{
	$estado = 'error';
	$msg = 'Error al actualizar.';
}
$result['msg'] = $msg;
$result['estado'] = $estado;
echo json_encode($result);
?>
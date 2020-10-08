<?php
require_once('../../../../../receptionProduct/ReceptionProduct/src/controlador.php');
$id_producto = $_POST['id_producto'];
$control_mongo = new Controlador('mongodb');
$estado = 'success';
$msg = 'Producto descartado.';
$control_mongo->setDataBaseMongo('bodega');
$control_mongo->setCollection('productos_cambio_precios');
$control_mongo->conectarMongo();
$query = array('productId' => $id_producto);
$pn = $control_mongo->consultar($query);
if(!$control_mongo->eliminarCollection($query)){
	//$msg = 'Probemas al descartar.';
}
foreach ( $pn as $producto){
	//$msg .= $producto->productId;
}
$result['msg'] = $msg;
$result['estado'] = $estado;
echo json_encode($result);
?>
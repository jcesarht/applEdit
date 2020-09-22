<?php
require_once('../../../../../receptionProduct/ReceptionProduct/src/controlador.php');
$id_producto = $_POST['id_producto'];
$control_mongo = new Controlador('mongodb');
$estado = 'success';
$msg = 'Producto descartado.';
$control_mongo->setDataBaseMongo('bodega');
$control_mongo->setCollection('productos_nuevos');
$control_mongo->conectarMongo();
$query = array('productId' => $id_producto);
$update = array('$set'=> array('estado' => 'descartado'));
$result = $control_mongo->actualizar($query,$update);
$result['msg'] = $msg;
$result['estado'] = $estado;
echo json_encode($result);
?>
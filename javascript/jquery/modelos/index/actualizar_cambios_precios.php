<?php
require_once('../../../../../receptionProduct/ReceptionProduct/src/controlador.php');
require_once('../../../../../receptionProduct/ReceptionProduct/src/connectwoo.php');
$msg='';
$estado = 'success';
$id_producto = $_POST['id_producto'];
$wp_id = $_POST['id_producto_woo'];
$precio = $_POST['precio'];
$precio_competencia = $_POST['precio_competencia'];
$control_market = new Controlador('mongodb');
$control_market->setDataBaseMongo('wp_market');
$control_market->setCollection('productos');
$control_market->conectarMongo();
$control_mongo = new Controlador('mongodb');
$control_mongo->setDataBaseMongo('bodega');
$control_mongo->setCollection('productos_cambio_precios');
$control_mongo->conectarMongo();
$cw = new Connectwoo();
$encuentra = array('wp_id' => $wp_id);
$result = $control_market->consultar($encuentra);
$check = false;
foreach ( $result as $wordpress ){
	$data = [
		'regular_price' => $precio_competencia,
	    'sale_price' => $precio
	];
	if($cw->setProduct($wp_id,$data)){
		$actualiza = array('$set'=> $data);
        $control_market->actualizar($encuentra,$actualiza);
        $encuentra = array('productId' => $id_producto);
        $control_mongo->eliminarCollection($encuentra); 
        $control_mongo->setCollection('productos');
		$control_mongo->conectarMongo();
		$data = ['storeSku.pricing.specialPrice' => "".$precio_competencia];
		$actualiza = array('$set'=> $data);
		$control_mongo->actualizar($encuentra,$actualiza); 
		$result2 = $control_mongo->consultar($encuentra);
        $product_temp = 'xxx';
        foreach($result2 as $temp){
            $product_temp = $temp->storeSku->pricing->specialPrice;
        }
        $msg = 'producto '.$product_temp.$wordpress->name.' <font size="+1"><strong>actualizado</strong></font>';       
        //$msg = 'producto '.$wordpress->name.' <font size="+1"><strong>actualizado</strong></font>';
	}
}
$resultado['msg'] = $msg;
$resultado['estado'] = $estado;
echo json_encode($resultado);
?>
<?php
require_once('../../../../../receptionProduct/ReceptionProduct/src/controlador.php');
require_once('../../../../../receptionProduct/ReceptionProduct/src/connectwoo.php');
$control_market = new Controlador('mongodb');
$control_market->setDataBaseMongo('wp_market');
$control_market->setCollection('productos');
$control_market->conectarMongo();
$control_mongo = new Controlador('mongodb');
$control_mongo->setDataBaseMongo('bodega');
$control_mongo->setCollection('productos_cambio_precios');
$control_mongo->conectarMongo();
$query = array('estado'=>'pendiente');
$pn = $control_mongo->consultar($query);
$result = array();
$estado = 'success';
$msg = '
<center><button onclick="buscarOverPrice()">Buscar Overprice</button></center>
<table border="1"><thead><tr><th>Imagen</th><th>Producto</th><th>Precio en la Tienda</th><th>Precio Homedepot</th><th>Acción</th></tr></thead><tbody>';
$x = 0;
foreach ( $pn as $producto){
	$precio_wpmarket = 0;
	$query = array('wp_id'=> $producto->woocomerce_id);
	$result = $control_market->consultar($query);
    foreach ( $result as $producto_market){
    	$precio_wpmarket = (float)$producto_market->sale_price;
    }
    $imagen_url = array("src"=>str_replace('<SIZE>','400',$producto->info->imageUrl));
	$msg .= '<tr id="tr_'.$x.'"><td ><img src="'.str_replace('<SIZE>','400',$producto->info->imageUrl).'" width="60px"></td><td>'.$producto->info->productLabel.'</td><td><input type="number" id="precio_'.$x.'" name="precio_'.$x.'" value="'.$precio_wpmarket.'"></td><td>
		<input type="hidden" id="precio_competencia_'.$x.'" name="precio_competencia_'.$x.'" value="'.($producto->storeSku->pricing->specialPrice).'">
		<input type="hidden" id="id_product_'.$x.'" name="id_product_'.$x.'" value="'.$producto->productId.'">$'.$producto->storeSku->pricing->specialPrice.'</td><td><a href="javascript:void(0);" onclick="actualizar(\''.$x.'\');">Actualizar</a> <a href="javascript:void(0);" onclick="descartarN(\''.$x.'\');">Descartar</a></td></tr>';
	$x++;
}
$msg .= '</tbody><table>';
$resultado['msg'] = $msg;
$resultado['estado'] = $estado;
echo json_encode($resultado);
?>
<?php
require_once('../../../../../receptionProduct/ReceptionProduct/src/controlador.php');
require_once('../../../../../receptionProduct/ReceptionProduct/src/connectwoo.php');
$control_mongo = new Controlador('mongodb');
$control_mongo->setDataBaseMongo('bodega');
$control_mongo->setCollection('productos_nuevos');
$control_mongo->conectarMongo();
$query = array('estado'=>'pendiente');
$pn = $control_mongo->consultar($query);
$result = array();
$estado = 'success';
$msg = '<table border="1" heigth="100%"><thead><tr><th>Imagen</th><th>Producto</th><th>Cantidad</th><th>Precio en la Tienda</th><th>Precio Homedepot</th><th>Acción</th></tr></thead><tbody>';
$x = 0;
foreach ( $pn as $producto){
    $imagen_url = array("src"=>str_replace('<SIZE>','400',$producto->info->imageUrl));
	$msg .= '<tr id="tr_'.$x.'"><td ><img src="'.str_replace('<SIZE>','400',$producto->info->imageUrl).'" width="60px"></td><td>'.$producto->info->productLabel.'</td><td><input type="number" id="cantidad_'.$x.'" name="cantidad_'.$x.'" value="1"></td><td><input type="number" id="precio_'.$x.'" name="precio_'.$x.'" value="'.($producto->storeSku->pricing->specialPrice * 0.85).'"></td><td>
		<input type="hidden" id="precio_anterior_'.$x.'" name="precio_anterior_'.$x.'" value="'.($producto->storeSku->pricing->specialPrice).'">
		<input type="hidden" id="id_product_'.$x.'" name="id_product_'.$x.'" value="'.$producto->productId.'">$'.$producto->storeSku->pricing->specialPrice.'</td><td><div id="accion_'.$x.'"></div><a id="boton_insertar_'.$x.'" href="javascript:void(0);" onclick="insertar(\''.$x.'\');">Insertar</a> <a id="boton_descartar_'.$x.'" href="javascript:void(0);" onclick="descartarN(\''.$x.'\');">Descartar</a></div></td></tr>';
	$x++;
}
$msg .= '</tbody><table>';
$resultado['msg'] = $msg;
$resultado['estado'] = $estado;
echo json_encode($resultado);
?>
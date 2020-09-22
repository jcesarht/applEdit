<?php
include_once('../../../../../receptionProduct/ReceptionProduct/src/dbmodel.php');
require_once('../../../../../receptionProduct/ReceptionProduct/src/controlador.php');
require_once('../../../../../receptionProduct/ReceptionProduct/src/connectwoo.php');
$cw = new Connectwoo();
$database = new ConexionBaseDeDatos();
$database->setDataBase('wp_prueba');
$database->conectarWoo();
$control_mongo = new Controlador('mongodb');
$control_wordpress_market = new Controlador('mongodb'); 
$control_wordpress_market->setDataBaseMongo('wp_market'); 
$control_mongo->setDataBaseMongo('bodega');
$control_mongo->setCollection('productos_nuevos');
$control_mongo->conectarMongo();
$resultado = array();
$estado = 'error';
$msg = 'No se agregó el producto';
$id_producto = $_POST['id_producto'];
$precio = $_POST['precio'];
$precio_anterior = $_POST['precio_anterior']; 
$query = array('productId' => $id_producto);
$producto_nuevo = $control_mongo->consultar($query);
foreach ( $producto_nuevo as $product){
    $nombre_marca = trim($product->info->brandName);
    $control_wordpress_market->setCollection('marcas');
    $control_wordpress_market->conectarMongo();
    $query = array('brandName' => $nombre_marca);
    $result = $control_wordpress_market->consultar($query);
    $check_marcas = false;
    $id_marca = '';
    foreach ( $result as $marca_temp){              
        $check_marcas = true;
        $id_marca = $marca_temp->wp_id;
    }
    if(!$check_marcas){
        $database->crearMarca($nombre_marca);
        $id_marca = $database->getIdMarca();
        $insertar = [array('brandName' => $nombre_marca,'wp_id' => "".$id_marca)];
        $control_wordpress_market->setDatos($insertar);
        $control_wordpress_market->insertarDatos($insertar);
    }
    $precio = $product->storeSku->pricing->originalPrice;
    if(isset($product->storeSku->pricing->specialPrice)){
        $precio = $product->storeSku->pricing->specialPrice;
    }
    $topAttributes = $product->info->topAttributes;
    $total_topAttributes = count($topAttributes);
    $control_wordpress_market->setCollection('atributos');
    $control_wordpress_market->conectarMongo();
    $att = array();
    $value_term = array();
    //aquí se verifica los atributos de cada producto
    $descripcion = '<ul>';
    for($at=0;$at < $total_topAttributes; $at++){
        $nombre_atributo = trim("".$topAttributes[$at]->name);
        $query = array('name' => $nombre_atributo);
        $result = $control_wordpress_market->consultar($query);
        $check_atributo = false;
        $valor_atributo = '';
        foreach ( $result as $atributo_temp){
            $check_atributo = true;
            $wp_atributo_id = $atributo_temp->wp_id;
            $valor_atributo = '-';
            if(isset($topAttributes[$at]->attributeValues[0]->attributeValue)){
                $valor_atributo = "".$topAttributes[$at]->attributeValues[0]->attributeValue;
            }
            $indice = array_search($valor_atributo, (array)$atributo_temp->term);
            //array_search devuelve un false o un número. El siguiente if valida si $indice es un boleano, lo que se asume que es false, de lo contrario $indice sería número
            $term = ['name' => $valor_atributo];
            if(is_bool($indice)){                                
                if(!$cw->setTermAtributo($wp_atributo_id,$term)){
                    echo "Problemas con guardar Term";
                    exit();
                }
                $actualiza = (array)$atributo_temp->term;
                array_push($actualiza, $valor_atributo);
                $actualiza = array('$set'=>array('term' => $actualiza));
                $encuentra = array('name' => $atributo_temp->name);
                $control_wordpress_market->actualizar($encuentra,$actualiza,array('upsert'=>true));
            }
            $term = [$valor_atributo];
            $att[] = ["id"=> "".$wp_atributo_id,"options"=> $term];
        }
        //el siguiente if crea un atributo en caso no exista
        if(!$check_atributo){
            if($cw->setAtributo(array("name"=> substr($nombre_atributo,0,28)))){
                $wp_atributo_id = $cw->getIdAtributo();
                $valor_atributo = '-';
                if(isset($topAttributes[$at]->attributeValues[0]->attributeValue)){
                    $valor_atributo = "".$topAttributes[$at]->attributeValues[0]->attributeValue;
                    $term = [
                       "name" =>$valor_atributo,
                    ];
                }
                $term = ['name' => $valor_atributo];
                if(!$cw->setTermAtributo($wp_atributo_id,$term)){
                    echo "Problemas con guardar Term";
                    exit();
                }
                $term = [$valor_atributo];
                $att[] = ["id"=> "".$wp_atributo_id,"options"=> $term];
                $actualiza = array('$set' => array('term' => $term, 'wp_id' => $wp_atributo_id));
                $encuentra = array('name' => $nombre_atributo);
                $control_wordpress_market->actualizar($encuentra,$actualiza,array('upsert' => true));
            }else{
                echo '500 No se guardó el atributo';
                exit();
            }
        }
        $descripcion .= '<li>'.$nombre_atributo.": <strong>".$valor_atributo."</strong></li>";
    }
    $descripcion .= '</ul>';
    //preparamos para insertar producto
    $array_img = array();
    //condicion para que analice las imagenes de cada producto
    $imagen_url = array("src"=>str_replace('<SIZE>','400',$product->info->imageUrl)); //se declara la variable y el tamaño para la imagen
    array_push($array_img, $imagen_url);
    if(isset($product->info->secondaryimageUrl)){ //se verifa la existenia de segunda imagen , si esta que la muestre y sino que lo salte
        $imagen_url = array("src"=>str_replace('<SIZE>','400',$product->info->secondaryimageUrl));
        array_push($array_img, $imagen_url); //relacion entre array
    }
    $producto = array(); //array por cada product
    $nombre_producto = $product->info->brandName.' '.$product->info->productLabel;
    
    if(isset($product->info->storeSkuNumber)){
        $producto = [
        'display'=> $product->info->brandName,
        'name' => $nombre_producto,
        'type' => 'simple',
        'price'=> "".$precio,
        'regular_price'=> "".$precio,
        'sale_price'=> "".($precio * 0.85),
        'description' => $descripcion,
        'short_description' => $descripcion,
        'sku' => "".$product->info->storeSkuNumber,
        'categories' => [
            [
              'id' => $product->categoryID,
            ],
         ],
         'attributes' => $att,
         'images' => $array_img,
        ];
    }else{
        $producto = [
        'display'=> $product->info->brandName,
        'name' => $nombre_producto,
        'type' => 'simple',
        'price'=> "".$precio,
        'regular_price'=> "".$precio,
        'sale_price'=> "".($precio * 0.85),
        'description' => $descripcion,
        'short_description' => $descripcion,
        'categories' => [
            [
              'id' => $product->categoryID,
            ],
         ],
         'attributes' => $att,
         'images' => $array_img,
        ];
    }
    if(!$cw->send($producto)){
        $check = false;
        break;
    }else{
        $id_product =  $cw->getIdProduct();
        @$product->woocomerce_id = $id_product;
        //prodcut viene con el id de mongo. debemos quitarselo para que lo genere automáticamnte en la proxima inserción de la colección bodega.productos
        unset($product->_id);
        $unidad = [$product];
        $control_mongo->setCollection('productos');
        $control_mongo->conectarMongo();
        $_id = $control_mongo->insertarDatos($unidad);
        $_id = $_id->getInsertedId();
        $control_mongo->setCollection('tiendas_productos');
        $control_mongo->conectarMongo();
        $unidad = [['tienda' => 'homedepot','bodega_id' => $_id, 'producto_id' => $product->productId]];
        $control_mongo->insertarDatos($unidad);
        @$producto['wp_id'] = $id_product;
        @$producto['bodega_id'] = $_id;
        $control_mongo->setCollection('productos_nuevos');
        $control_mongo->conectarMongo();
        $query = array('productId'=>$product->productId);
        $control_mongo->eliminarCollection($query);
        $database->relacionProductoMarca($id_product,$id_marca);
    }
    $productos_wordpress[] = $producto; 
    $control_wordpress_market->setCollection('productos');
    $control_wordpress_market->conectarMongo();
    $control_wordpress_market->insertarDatos($productos_wordpress);
    $estado = 'success';
    $msg = 'Producto <strong>'.$product->info->productLabel.'</strong> <font size="+2"><strong>Agregado</strong></font>';
}	
$resultado['msg'] = $msg;
$resultado['estado'] = $estado;
echo json_encode($resultado);
?>
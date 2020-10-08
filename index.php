<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title> Plantilla de Productos</title>
  <link rel="stylesheet" href="estilos.css">
  <script type="text/javascript" src="javascript/jquery.js"></script>
  <script type="text/javascript" src="javascript/js/index/index.js"></script>
  <script src="https://kit.fontawesome.com/12ffec3252.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
      
        <div class="contenedor">
            <header>
            <nav class="navbar navbar-expand-lg navbar-light bg-white">
                <a class="navbar-brand" href="#">
                <img src="img/logo.png" width="100%" height="55%" class="d-inline-block align-top mx-4 m" alt="" loading="lazy" ></a>

                <div class="collapse navbar-collapse" >
                        <ul class="navbar-nav px-5 mx-4" >   
                            <li class="nav-item mx-1">
                                <a class="nav-link active" href="#"><strong>Home</strong></a>
                            </li>
                            <li class="nav-item mx-2">
                                <a class="nav-link active" href="#" onclick="consultarProductosNuevos();" ><strong>Productos nuevos</strong></a>
                            </li>
                            <li class="nav-item mx-2 ">
                                <a class="nav-link active" href="#" onclick="consultarCambioPrecios();"><strong>Productos con cambio de precio</strong></a>
                                
                            </li>
                            <li class="nav-item mx-3 ">
                                <a class="nav-link active" href="https://bestwayapp.market/" ><strong>Ir a la cuenta</strong></a>
                            </li>
                            </ul>
                </div>
            </nav>

            </header>
            <center>
              <div id="div_alerta_r"></div>
              <div class ="container" id="contenido">
              </div>
            </center>
           


            









</body>



</html>
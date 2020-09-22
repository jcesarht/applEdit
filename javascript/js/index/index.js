var rqst = null;
var loc = window.location;
var msg = 'Sin respuesta';
var pathName = loc.pathname.substring(0, loc.pathname.lastIndexOf('/'));
var flag_overprice = 0;
//pathName = pathName.substring(0, pathName.lastIndexOf('/') + 1);
pathName += '/';
function errorAjax( jqXHR, textStatus, errorThrown ){
    if (jqXHR.status === 0) {
       msg = 'Not connect: Verify Network.';
    } else if (jqXHR.status == 404) {
       msg = 'Requested page not found [404].';
    } else if (jqXHR.status == 500) {
       msg = 'Internal Server Error [500].';
    } else if (textStatus === 'parsererror') {
       msg = 'Requested JSON parse failed.';
    } else if (textStatus === 'timeout') {
       msg = 'Time out error.';
    } else if (textStatus === 'abort') {
      msg = 'Ajax request aborted.';
    } else {
       msg = 'Uncaught Error: ' + jqXHR.responseText;
    }
    return msg;
}
function consultarCambioPrecios(){ 	
  var url = pathName + 'javascript/jquery/modelos/index/consultar_cambios_de_precios.php'; 
	var div_alerta = document.getElementById("div_alerta_r");
  if(rqst && rqst.readyState != 4) { 
      rqst.abort();
  }
  rqst = $.ajax({
          url:   url,
          type:  'post',
          beforeSend: function () {
            $("#contenido").html('<center><img src="'+pathName+'img/big_loading.gif" alt="Cargando..."></center>');
            div_alerta.setAttribute('class','');
            div_alerta.setAttribute('role','');
          },
          success: function (response) {
            console.log(response);
            var consulta = jQuery.parseJSON(response);
            var icono_alerta = '';
            var tipo_msg = '';
            if(consulta.estado == 'success'){
              $("#contenido").html(consulta.msg);
            }else{
              if(consulta.estado == 'warning'){
                icono_alerta = "fa fa-exclamation-triangle";
              }else if(consulta.estado == 'info'){
                icono_alerta = "fa fa-info-circle";
              }
              else{
                icono_alerta = "fa fa-times-circle";
              }
              tipo_msg = 'alert alert-'+consulta.estado+' alert-dismissible fade in';
              div_alerta.setAttribute('class',tipo_msg);
              div_alerta.setAttribute('role','alert');  
              div_alerta.innerHTML = consulta.msg;  
              //$("#contenido_consulta").html("");
            } 
          }
    }).fail( function( jqXHR, textStatus, errorThrown ) { 
        $("#contenido").html(errorAjax( jqXHR, textStatus, errorThrown ));     
	});
}
function buscarOverPrice(){
  var check = true;
  var cont = 0;
  var tr = '';
  if(flag_overprice == 0){
    flag_overprice = 1;
    while(check){
      $precio_publicado = parseFloat($("#precio_"+cont).val());
      $precio_competencia =  parseFloat($("#precio_competencia_"+cont).val());
      if($("#precio_"+cont).length){
        if( $precio_publicado < $precio_competencia){
          tr = document.getElementById("tr_"+cont);
          tr.style.display='none';
        }
        cont++;
      }else{
        check = false;
      }
    }
  }else{
    flag_overprice = 0;
    while(check){
      if($("#precio_"+cont).length){
        tr = document.getElementById("tr_"+cont);
        tr.style.display='';
        cont++;
      }else{
        check = false;
      }
    }
  }
}
function actualizar(id_element){  
  if(confirm('¿Desea actualizar este producto?')){
    var url = pathName + 'javascript/jquery/modelos/index/actualizar_cambios_precios.php'; 
    var input = document.getElementById("precio_"+id_element);
    var precio_competencia = document.getElementById("precio_anterior_"+id_element);
    var id_product_woo = document.getElementById('woo_'+id_element);
    var id_product = document.getElementById('id_product_'+id_element);
    var param = {
      "precio": input.value,
      "precio_competencia": precio_competencia.value,
      "id_producto": id_product.value,
      "id_producto_woo": id_product_woo.value
    };
    var tr = document.getElementById("tr_"+id_element);
    if(rqst && rqst.readyState != 4) { 
        rqst.abort();
    }
    rqst = $.ajax({
            url:   url,
            data: param,
            type:  'post',
            beforeSend: function () {
              tr.innerHTML = '<td colspan="5"><center><img src="'+pathName+'img/big_loading.gif" alt="Cargando..." width="50px"></center></td>';
            },
            success: function (response) {
              console.log(response);
              var consulta = jQuery.parseJSON(response);
              tr.innerHTML = '<td colspan="5"><center>'+consulta.msg+'</center></td>'; 
            }
      }).fail( function( jqXHR, textStatus, errorThrown ) { 
          $("#contenido").html(errorAjax( jqXHR, textStatus, errorThrown ));     
    });
  }
}
function descartar(id_element){  
  if(confirm('¿Desea descartar este producto?')){
    var url = pathName + 'javascript/jquery/modelos/index/descartar_cambios_de_precio.php'; 
    var id_product = document.getElementById('id_product_'+id_element);
    var param = {
      "id_producto": id_product.value,
    };
    var tr = document.getElementById("tr_"+id_element);
    if(rqst && rqst.readyState != 4) { 
        rqst.abort();
    }
    rqst = $.ajax({
            url:   url,
            data: param,
            type:  'post',
            beforeSend: function () {
              tr.innerHTML = '<td colspan="5"><center><img src="'+pathName+'img/big_loading.gif" alt="Cargando..." width="50px"></center></td>';
            },
            success: function (response) {
              var consulta = jQuery.parseJSON(response);
              tr.innerHTML = '<td colspan="5"><center>'+consulta.msg+'</center></td>'; 
            }
      }).fail( function( jqXHR, textStatus, errorThrown ) { 
          $("#contenido").html(errorAjax( jqXHR, textStatus, errorThrown ));     
    });
  }
}
function consultarProductosNuevos(){  
  var url = pathName + 'javascript/jquery/modelos/index/consultar_productos_nuevos.php'; 
  var div_alerta = document.getElementById("div_alerta_r");
  if(rqst && rqst.readyState != 4) { 
      rqst.abort();
  }
  rqst = $.ajax({
          url:   url,
          type:  'post',
          beforeSend: function () {
            $("#contenido").html('<center><img src="'+pathName+'img/big_loading.gif" alt="Cargando..."></center>');
            div_alerta.setAttribute('class','');
            div_alerta.setAttribute('role','');
          },
          success: function (response) {
            console.log(response);
            var consulta = jQuery.parseJSON(response);
            var icono_alerta = '';
            var tipo_msg = '';
            if(consulta.estado == 'success'){
              $("#contenido").html(consulta.msg);
            }else{
              if(consulta.estado == 'warning'){
                icono_alerta = "fa fa-exclamation-triangle";
              }else if(consulta.estado == 'info'){
                icono_alerta = "fa fa-info-circle";
              }
              else{
                icono_alerta = "fa fa-times-circle";
              }
              tipo_msg = 'alert alert-'+consulta.estado+' alert-dismissible fade in';
              div_alerta.setAttribute('class',tipo_msg);
              div_alerta.setAttribute('role','alert');  
              div_alerta.innerHTML = consulta.msg;  
              //$("#contenido_consulta").html("");
            } 
          }
    }).fail( function( jqXHR, textStatus, errorThrown ) { 
        $("#contenido").html(errorAjax( jqXHR, textStatus, errorThrown ));     
  });
}
function descartarN(id_element){  
  if(confirm('¿Desea descartar este producto?')){
    var url = pathName + 'javascript/jquery/modelos/index/descartar_productos_nuevos.php'; 
    var id_product = document.getElementById('id_product_'+id_element);
    var param = {
      "id_producto": id_product.value,
    };
    var tr = document.getElementById("tr_"+id_element);
    if(rqst && rqst.readyState != 4) { 
        rqst.abort();
    }
    rqst = $.ajax({
            url:   url,
            data: param,
            type:  'post',
            beforeSend: function () {
              tr.innerHTML = '<td colspan="5"><center><img src="'+pathName+'img/big_loading.gif" alt="Cargando..." width="50px"></center></td>';
            },
            success: function (response) {
              console.log(response);
              var consulta = jQuery.parseJSON(response);
              tr.innerHTML = '<td colspan="5"><center>'+consulta.msg+'</center></td>'; 
            }
      }).fail( function( jqXHR, textStatus, errorThrown ) { 
          $("#contenido").html(errorAjax( jqXHR, textStatus, errorThrown ));     
    });
  }
}
function insertar(id_element){  
  if(confirm('¿Desea añadir este producto?')){
    var url = pathName + 'javascript/jquery/modelos/index/insertar_productos_nuevo.php'; 
    var id_product = document.getElementById('id_product_'+id_element);
    var precio = document.getElementById('precio_'+id_element);
    var precio_anterior = document.getElementById('precio_anterior_'+id_element);
    var param = {
      "id_producto": id_product.value,
      "precio": precio.value,
      "precio_anterior": precio_anterior.value
    };
    var tr = document.getElementById("tr_"+id_element);
    if(rqst && rqst.readyState != 4) { 
        rqst.abort();
    }
    rqst = $.ajax({
            url:   url,
            data: param,
            type:  'post',
            beforeSend: function () {
              tr.innerHTML = '<td colspan="5"><center><img src="'+pathName+'img/big_loading.gif" alt="Cargando..." width="50px"></center></td>';
            },
            success: function (response) {
              console.log(response);
              var consulta = jQuery.parseJSON(response);
              tr.innerHTML = '<td colspan="5"><center>'+consulta.msg+'</center></td>'; 
            }
      }).fail( function( jqXHR, textStatus, errorThrown ) { 
          $("#contenido").html(errorAjax( jqXHR, textStatus, errorThrown ));     
    });
  }
}
var lat, lon, marker, map, clientMarker, client_lat, client_lon, cliente;
var chegou = "no";

function mudar_ocupado(){
    
   $.ajax({ 
      type: "POST", 
      url: "http://tfctaxi.hostzi.com/estado.php", 
      //data: "nome=helena",
      data: { estado:"ocupado", user: localStorage.getItem("username")},
      dataType: 'json',
      success: function(data){
            console.log("Estou ocupado!");
            
            $("#mudar_ocupado").show();
            $("#mudar_livre").hide();
            
         },
      error: function(jqXHR, textStatus, errorThrown ){
         console.log("POST mudar_estado: ", jqXHR, textStatus, errorThrown);
      }
   }); 
};

function mudar_livre(){
    
   $.ajax({ 
      type: "POST", 
      url: "http://tfctaxi.hostzi.com/estado.php", 
      //data: "nome=helena",
      data: { estado:"livre", user: localStorage.getItem("username")},
      dataType: 'json',
      success: function(data){
         console.log("Estou livre");
         
         $("#mudar_ocupado").hide();
         $("#mudar_livre").show();
      },
      error: function(jqXHR, textStatus, errorThrown ){
         console.log("POST mudar_estado: ", jqXHR, textStatus, errorThrown);
      }
   }); 
};



function initEstado(){
   
   $.ajax({ 
      type: "POST", 
      url: "http://tfctaxi.hostzi.com/verificar_estado.php", 
      data: { user: localStorage.getItem("username")},
      dataType: 'json',
      success: function(data){
         
         if (data) {
            if(data.estado === "livre"){
               console.log("Estado livre");
               $("#mudar_ocupado").hide();
               $("#mudar_livre").show();
            }else if(data.estado === "ocupado"){
               console.log("Estado ocupado");
               $("#mudar_ocupado").show();
               $("#mudar_livre").hide();
               
            }
            else{
               console.log("Estado inválido");
            }
         }
      },
      error: function(jqXHR, textStatus, errorThrown ){
         console.log("POST mudar_estado: ", jqXHR, textStatus, errorThrown);
      }
   }); 
  
  //$("#estado").html('<a id="alterar" href="#" data-role="button" data-icon="grid" class="ui-btn-right" onclick="mudar_estado()">Chamar Taxi</a>');
  //$("#estado").html('<a id="logout" href="index.html" data-role="button" data-icon="search" class="ui-btn-right">Sair</a>');
}

function concluir_servico() {
   $.ajax({ 
      type: "POST", 
      url: "http://tfctaxi.hostzi.com/end_service.php", 
      //data: "nome=helena",
      data: { cliente: cliente, user: localStorage.getItem("username")},
      success: function(){
         console.log("Servico concluido");
         $("#concluir").hide();
         $("#concluir").hide();
         
      },
      error: function(jqXHR, textStatus, errorThrown ){
         console.log("POST arrived: ", jqXHR, textStatus, errorThrown);
      }
   });

   	//$("#estado").html(estado_div);
	$("#mudar_ocupado").hide();
        $("#mudar_livre").show();
	chegou="no";//variavel para garantir que o serviço é completo e perceber se foi cancelado

}

function arrived() {//mudar estado de enroute para done, significando que o taxista chegou ao local
   chegou = "sim";
   $.ajax({ 
      type: "POST", 
      url: "http://tfctaxi.hostzi.com/end.php", 
      //data: "nome="+cliente,
      data: { user: cliente },
      success: function(){
            console.log("Taxi chegou");
            $("#chegou").hide();
            $("#concluir").show();
            $("#controlos").hide();
            clientMarker.setMap(null);//limpar marker do cliente do mapa
         },
      error: function(jqXHR, textStatus, errorThrown ){
      console.log("POST arrived: ", jqXHR, textStatus, errorThrown);
      }
   });
   
   
}

 function aceitar() {
   
   // Se aceitar fica com o botao de chegar visivel
   
   
   $.ajax({ 
      type: "POST", 
      url: "http://tfctaxi.hostzi.com/enroute.php", 
      data: { cliente: cliente, user: localStorage.getItem("username")},
      dataType: 'json',
      success: function(res){
         if (res.pedido == "sim") {
            console.log("taxi a caminho");
         }else{
            console.log("cliente perdido");
         }
         
         $("#chegou").show();
         $("#controlos").hide();
      },
      error: function(jqXHR, textStatus, errorThrown ){
         console.log("POST aceitar: ", jqXHR, textStatus, errorThrown);
      }
   });
   analisar_pedido();
 }
 
 function negar() {
   console.log("negar cliente");
   concluir_servico();//rejeitar cliente, remover dados sobre pedido
   clientMarker.setMap(null);//limpar marker do cliente do mapa

   $("#controlos").hide();
 }
 
 function analisar_pedido() {
   $.ajax({ 
      type: "POST", 
      url: "http://tfctaxi.hostzi.com/analisar.php", 
      data: "nome="+cliente,
      dataType: 'json',
      success: function(res){
            console.log("analisar_pedido: ", res.estado);
            
            // cliente cancelou
            if (res.estado === "done") {
               if (chegou == "no") {
                  console.log("Pedido cancelado");
                  concluir_servico();
                   $("#estado").html(estado_div);
                   $("#controlos").html(ocupado);
                   clientMarker.setMap(null);//limpar marker do cliente do mapa
               } 
            // está à espera
            }else if(res.estado == "enroute"){
               console.log("esta enroute");
               setTimeout(analisar_pedido, 5000);
            }
            else{
               console.log("Analise (erro): ", res.estado);
            }
         },
      error: function(jqXHR, textStatus, errorThrown ){
         console.log("POST analisar: ", jqXHR, textStatus, errorThrown);
      }
   });
 }
 
 function guardar_posicao() {//guarda posição do taxista na base de dados e verifica se tem pedidos
    $.ajax({ 
      type: "POST",
      url: "http://tfctaxi.hostzi.com/pos.php",
      data: { lat: lat, lon: lon, user: localStorage.getItem("username")} ,
      dataType: 'json',
      success: function(res){  
         console.log(res.pedidos);
         if (res.pedidos.estado == "sim") {
         
            client_lat = res.cliente.lat;
            client_lon = res.cliente.lon;
            cliente = res.cliente.id_utilizador;
            console.log("Cliente: " + cliente + " na posição lat: "+client_lat+" lon: "+client_lon);
            
            trace_client();
            
            document.getElementById('audiotag1').play();
               
            //$("#controlos").html(controlos);
            // se tiver um cliente, aceita ou nega serviço
            $("#controlos").show();
         }
      },     
      error: function(res, textStatus, errorThrown ){
         console.log(res.status);
         if (res && res.status === 302) {
               window.location.href = res.getResponseHeader('Location');
            }
      }
    }); 
 }//guardar posiçao end ************

   function getPos() {//função inicial para ler posição do taxista
         estado = "livre";
         navigator.geolocation.getCurrentPosition(onSuccess, onError, {enableHighAccuracy:true});
         setTimeout(keep_alive, 10000); //reler funçao de 10 em 10 segundos
 }
 
 function onSuccess(position) {//lê mapa e marca marker do taxista no mapa
    lat = position.coords.latitude;
    lon = position.coords.longitude;
    console.log("Found - LAT: ", lat, "LON: ", lon);
    
    var mapoptions = {
        zoom: 16,
        center: new google.maps.LatLng(lat,lon),
        mapTypeId: google.maps.MapTypeId.ROADMAP,
    };
    map = new google.maps.Map(document.getElementById("map"), mapoptions);
    marker = new google.maps.Marker({
        position: new google.maps.LatLng(lat,lon),
        map: map,
        icon: 'img/taxi.png'
    });
    guardar_posicao();
}

function keep_alive() {//lê posição do taxista e marca o seu ponto no mapa
   navigator.geolocation.getCurrentPosition(onRefresh, onError, {enableHighAccuracy:true});
   guardar_posicao();
   setTimeout(keep_alive, 10000); //reler funçao de 10 em 10 segundos   
}

//quando actualiza posição do taxista, actualiza também o marker na sua localização
function onRefresh(position) {
   lat = position.coords.latitude;
   lon = position.coords.longitude;
   
   console.log("Found - LAT: ", lat, "LON: ", lon);
   
   marker.setPosition(new google.maps.LatLng(lat, lon));//actualizar ponto no mapa
   map.setCenter(new google.maps.LatLng(lat, lon));//centrar mapa
}

function trace_client() {//marca ponto no mapa para posição de cliente
   if (clientMarker != null) {
      clientMarker.setMap(null);//limpar marker do cliente do mapa
   }
   clientMarker = new google.maps.Marker({
        position: new google.maps.LatLng(client_lat,client_lon),
        map: map
    });
   map.setCenter(new google.maps.LatLng(lat, lon));//centrar mapa
   console.log("Cliente marcado no mapa");
}

function onError(error) {
   console.log('code: '    + error.code, 'message: ' + error.message);
}
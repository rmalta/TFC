var lat, lon, map, taxiMarker, taxi_lat, taxi_lon, taxista;
var arrived = "no";//variavel para conferir se o taxi chegou ou não

function pos_click(){
    
$.ajax({ 
   type: "POST", 
   url: "http://tfctaxi.hostzi.com/call.php", 
   data: { lat: lat, lon: lon, user: localStorage.getItem("username")},
   success: function(data){

     $("#mensagem").text("À procura de um taxi. Aguarde por favor.");
     $("#chamar_taxi").hide();
     
     setTimeout(check_status, 16000); //ler função ao fim de 16 segundos para verificar se já tem taxista
   },
   error: function(jqXHR, textStatus, errorThrown ){
         console.log("POST pos_click: ", jqXHR, textStatus, errorThrown);
   }
 });//fim do ajax 
 };//fim da funçao pos_click()
 
function initEstado(){

  $("#chamar_taxi").show();
}

function clear_bd() {//apagar dados da chamada da bd
   $.ajax({ 
   type: "POST", 
   url: "http://tfctaxi.hostzi.com/clear.php", 
   data: { user: localStorage.getItem("username")},
   success: function(data){ 
     console.log("BD: Dados da chamada apagados");
   },
   error: function(jqXHR, textStatus, errorThrown ){
         console.log("POST clear_bd: ", jqXHR, textStatus, errorThrown);
      }
 });//fim do ajax 
}

function check_status() {//verifica se pedido já foi correspondido
   $.ajax({ 
   type: "POST", 
   url: "http://tfctaxi.hostzi.com/check.php", 
   data: { user: localStorage.getItem("username")},
   dataType: 'json',
   success: function(data){ 
     if (data.id_utilizador != "no") {//se já tem taxista(if true)
      console.log("Pedido atendido por: "+data.nome);
      
      $("#mensagem").text("taxi a caminho!");
      
      taxi_lat = data.lat;
      taxi_lon = data.lon;
      taxista = data.id_utilizador;
      trace_taxi();   
      //meter taxista no mapa e chamar função para verificar a sua posição de 10 em 10s
      //chamar funçao para meter marker do taxista no mapa e actualizar a sua posição de x em x seg
     }else{
           console.log("A contactar novo taxista...");
           
           //apagar dados correspondentes a ultima chamada(fazer outro ajax)
           clear_bd();//apagar dados da chamada não correspondida
           pos_click();//chamar novo taxi
     };
   },
   error: function(jqXHR, textStatus, errorThrown ){
         console.log("POST check_status: ", jqXHR, textStatus, errorThrown);
      }
 });//fim do ajax 
}//fim da função check_status()

//funçoes para o fim, quando o taxi chega ao local, chegou para indicar que o taxista
//realmente chegou ao local, e falhou para indicar que taxista falhou ao recolher cliente
function chegou() {
   
   $("#chegou").hide();
   $("#chamar_taxi").show();//limpar dados da tabela
   $("#mensagem").text("");
   console.log("O taxi chegou :)");
   taxiMarker.setMap(null);//limpar marker do taxista do mapa
}

function falhou() {

   $("#chegou").hide();
   $("#chamar_taxi").show();//limpar dados da tabela
   $("#mensagem").text("");
   console.log("O taxi nao chegou :(");
   taxiMarker.setMap(null);//limpar marker do taxista do mapa
}
 function getPos() {
    navigator.geolocation.getCurrentPosition(onSuccess, onError, {enableHighAccuracy:true});
 }
 
 function onSuccess(position) {
    lat = position.coords.latitude;
    lon = position.coords.longitude;
    
    var image = '/img/taxi_green.png';
    var currentposition = new google.maps.LatLng(lat,lon);
    var mapoptions = {
        zoom: 16,
        center: currentposition,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        icon: image
    };
   map = new google.maps.Map(document.getElementById("map"), mapoptions);
    var marker = new google.maps.Marker({
        position: currentposition,
        map: map
    });
   // pos_click();
}//fim da função onSuccess

function trace_taxi() {//marca ponto no mapa para posição do taxista
   taxiMarker = new google.maps.Marker({
        position: new google.maps.LatLng(taxi_lat, taxi_lon),
        map: map
    });
   console.log("Taxista marcado no mapa");
   setTimeout(refresh_taxi, 12000);
}

function refresh_taxi() {//actualizar ponto do taxista no mapa
   taxiMarker.setPosition(new google.maps.LatLng(taxi_lat, taxi_lon));//actualizar ponto no mapa
   console.log("taxista actualizado");
   //verificar se já chegou ao local com variavel global, se nao actualiza chama de novo função, se sim para
   check_ride();
}

function get_location() {
   $.ajax({ 
      type: "POST", 
      url: "http://tfctaxi.hostzi.com/taxi_pos.php", 
     // data: "nome="+taxista,
      data: { nome: taxista },
      dataType: 'json',
      success: function(data){
            taxi_lat = data.lat;
            taxi_lon = data.lon;
         },
      error: function(jqXHR, textStatus, errorThrown ){
         console.log("POST get_location: ", jqXHR, textStatus, errorThrown);
      }
   });
}

function check_ride() {//verifica se o taxista já chegou, senão, recebe a sua posição 
   $.ajax({ 
      type: "POST", 
      url: "http://tfctaxi.hostzi.com/ride.php", 
      //data: "nome=rui",
      data: { user: localStorage.getItem("username")}, 
      dataType: 'json',
      success: function(data){
         if (arrived == "no") {
            if (data.estado == "done") {
              
              $("#chegou").show();
              
              arrived = "sim";
            }
            else{
               get_location();
            }
         }//if arrived == no
         },
      error: function(jqXHR, textStatus, errorThrown ){
         console.log("POST check_ride: ", jqXHR, textStatus, errorThrown);
      }
   }); 
   if (arrived == "no") {
      setTimeout(refresh_taxi, 12000);
   }
}

function onError(error) {
    alert('code: '    + error.code    + '\n' +
          'message: ' + error.message + '\n');
}
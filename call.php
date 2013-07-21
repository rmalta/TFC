<?php
include "config.php";
error_reporting( E_ALL );

//use this header in order to allow any origin to access the resource
header('Access-Control-Allow-Origin: *');

//guardar na base de dados pedidos dos clientes
$lat = $_REQUEST['lat'];
$lon = $_REQUEST['lon']; 
$username = $_REQUEST['user'];//recebe username do cliente

mysql_connect($GLOBALS['config']["db"]["hostname"], $GLOBALS['config']["db"]["username"], $GLOBALS['config']["db"]["password"]) or die ("Could not connect: " . mysql_error());
mysql_select_db($GLOBALS['config']["db"]["database"]);

//procura utilizador na bd | para cliente

$get_id= mysql_query("SELECT * FROM utilizadores WHERE nome='$username' and id_tipo_utilizador='1'");
$utilizadores= mysql_fetch_assoc($get_id);

$id_utilizador = $utilizadores['id'];//encontra id do utilizador

$cliente_existe = mysql_query("select count(1) as total from clientes where id_utilizador = '$id_utilizador'");

$rows_array = mysql_fetch_assoc($cliente_existe);
$rows = $rows_array["total"];


if($rows > 0){
    
    $result = mysql_query("UPDATE clientes SET lat='$lat', lon='$lon', estado='waiting' WHERE id_utilizador='$id_utilizador'");
    //insere cliente para waiting(em espera)
}else{
    
    //actualizar cliente para waiting(em espera)
    error_log("call.php query: " . "INSERT INTO clientes (lat, lon, id_utilizador, estado) VALUES ('$lat','$lon', $id_utilizador, 'waiting')" , 0);
    $result = mysql_query("INSERT INTO clientes (lat, lon, id_utilizador, estado) VALUES ('$lat','$lon', $id_utilizador, 'waiting')");
}



//procurar taxi mais proximo do cliente    
$nearPoints = mysql_query("SELECT id,
                            lat,
                            lon,
                            id_utilizador,
                            round((((acos(sin((".$lat."*pi()/180)) *
                            sin((lat*pi()/180))+cos((".$lat."*pi()/180))
                            * cos((lat*pi()/180)) * cos(((".$lon."- lon)
                            *pi()/180))))*180/pi())*60*1.1515*1.609344),3) as distance
                            FROM taxistas WHERE disponibilidade ='livre' HAVING distance <=1
                            ORDER BY distance ASC LIMIT 1"
                    );
    
$array = mysql_fetch_assoc($nearPoints);

error_log("O utilizador ". $id_utilizador . " esta a fazer um pedido ao taxista " .$array['id_utilizador'] , 0);


$id_taxista = $array['id_utilizador'];//guardar nome numa variavel, ao fazer query sql ler o array nao funciona

// senao estiver null, meter o nome do taxista encontrado no pedido do cliente
if($id_taxista != null ){
    //meter o id do taxista na tabela clientes    
    $sql1 = "UPDATE clientes SET taxista='$id_taxista' WHERE id_utilizador='$id_utilizador'";
    $putid = mysql_query($sql1);//executar query

    //meter no taxista o id do cliente que fez o pedido, no campo pedidos
    $sql2 = "UPDATE taxistas SET pedidos='$id_utilizador' WHERE id_utilizador='$id_taxista'";  
    $row2 = mysql_query($sql2);//executar query
}
mysql_close();
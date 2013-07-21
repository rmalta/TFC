<?php
include "config.php";
//verifica se pedido já foi atendido
error_reporting( E_ERROR );

//use this header in order to allow any origin to access the resource
header('Access-Control-Allow-Origin: *');

$username = $_REQUEST['user'];

mysql_connect($GLOBALS['config']["db"]["hostname"], $GLOBALS['config']["db"]["username"], $GLOBALS['config']["db"]["password"]) or die ("Could not connect: " . mysql_error());
mysql_select_db($GLOBALS['config']["db"]["database"]);

//procura utilizador na bd | para cliente
$get_id= mysql_query("SELECT * FROM utilizadores WHERE nome='$username' and id_tipo_utilizador='1'");
$utilizadores= mysql_fetch_assoc($get_id);

$id_utilizador = $utilizadores['id'];//encontra id do utilizador
error_log("check: user: ". $id_utilizador , 0);
$query= mysql_query("SELECT * FROM clientes WHERE id_utilizador='$id_utilizador'");
$array= mysql_fetch_assoc($query);

$id_taxista = $array['taxista'];
error_log("check: taxista: ". $id_taxista , 0);
if($array && $array['estado'] == "enroute"){//verifica se pedido já foi correspondido
    $taxi= mysql_query("SELECT * FROM taxistas WHERE id_utilizador='$id_taxista'");
    $result= mysql_fetch_assoc($taxi);
    echo json_encode($result);//devolve dados do taxista
}else{
    $arr = array ('id_utilizador'=>'no');
    echo json_encode($arr);
}

mysql_close();
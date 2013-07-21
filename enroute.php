<?php
include "config.php";

//aceita pedido do cliente
error_reporting( E_ERROR );

//use this header in order to allow any origin to access the resource
header('Access-Control-Allow-Origin: *');

$username = $_REQUEST['user'];//recebe username do taxista
$cliente = $_REQUEST['cliente'];//recebe id do cliente

mysql_connect($GLOBALS['config']["db"]["hostname"], $GLOBALS['config']["db"]["username"], $GLOBALS['config']["db"]["password"]) or die ("Could not connect: " . mysql_error());
mysql_select_db($GLOBALS['config']["db"]["database"]);

//procura utilizador na bd | para taxista
$get_id= mysql_query("SELECT * FROM utilizadores WHERE nome='$username' and id_tipo_utilizador='2'");
$utilizadores= mysql_fetch_assoc($get_id);

$id_utilizador = $utilizadores['id'];//encontra id do utilizador

$sql= mysql_query("SELECT * FROM clientes WHERE id_utilizador='$cliente'");
$valor= mysql_fetch_assoc($sql);
error_log("enroute.php: ".$valor['estado'] ,0);
if($valor && $valor['estado'] == "waiting"){//verifica se cliente ainda está disponivel
    //altera pedido do cliente para enroute, como estando a caminho
    $query= mysql_query("UPDATE clientes SET estado='enroute' WHERE taxista='$id_utilizador'"); 
    $array= mysql_fetch_assoc($query);
    
    //mudar estado do taxista para ocupado
    $query= mysql_query("UPDATE taxistas SET disponibilidade='ocupado' WHERE id_utilizador='$id_utilizador'");
    $array2= mysql_fetch_assoc($query);
    
     $arr = array ( 'pedido'=>'sim' );
     echo json_encode($arr);
     
}else{//se já não está disponivel limpar dados
    //mudar estado do taxista para ocupado
    $query= mysql_query("UPDATE taxistas SET disponibilidade='livre', pedidos='no' WHERE id_utilizador='$id_utilizador'");
    $array2= mysql_fetch_assoc($query);
    
    $arr = array ( 'pedido'=>'no' );
    echo json_encode($arr);
}

mysql_close();
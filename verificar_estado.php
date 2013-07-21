<?php
include "config.php";
error_reporting( E_ALL );
//trocar estado do taxista

//use this header in order to allow any origin to access the resource
header('Access-Control-Allow-Origin: *');

$username = $_REQUEST['user'];//recebe username do taxista

mysql_connect($GLOBALS['config']["db"]["hostname"], $GLOBALS['config']["db"]["username"], $GLOBALS['config']["db"]["password"]) or die ("Could not connect: " . mysql_error());
mysql_select_db($GLOBALS['config']["db"]["database"]);

//procura utilizador na bd | para taxista
$get_id= mysql_query("SELECT * FROM utilizadores WHERE nome='$username' and id_tipo_utilizador='2'");
$utilizadores= mysql_fetch_assoc($get_id);
$id_utilizador = $utilizadores['id'];//encontra id do utilizador

$verificar = mysql_query("SELECT disponibilidade FROM taxistas WHERE id_utilizador='$id_utilizador'");
$estado= mysql_fetch_assoc($verificar);

echo json_encode(array("estado" => $estado['disponibilidade']));
    
mysql_close();
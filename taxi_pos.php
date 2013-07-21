<?php
include "config.php";
//verificar dados do taxista, saber onde ele está
error_reporting( E_ALL );

//use this header in order to allow any origin to access the resource
header('Access-Control-Allow-Origin: *');

$nome = $_REQUEST['nome'];

mysql_connect($GLOBALS['config']["db"]["hostname"], $GLOBALS['config']["db"]["username"], $GLOBALS['config']["db"]["password"]) or die ("Could not connect: " . mysql_error());
mysql_select_db($GLOBALS['config']["db"]["database"]);

$taxi= mysql_query("SELECT * FROM taxistas WHERE id_utilizador='$nome'");
$result= mysql_fetch_assoc($taxi);

echo json_encode($result);

mysql_close();
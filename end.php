<?php
include "config.php";
//finalizar trajecto do taxista, passar estado de enroute para done
error_reporting( E_ALL );

//use this header in order to allow any origin to access the resource
header('Access-Control-Allow-Origin: *');

$id_cliente = $_REQUEST['user'];//recebe username do taxista

mysql_connect($GLOBALS['config']["db"]["hostname"], $GLOBALS['config']["db"]["username"], $GLOBALS['config']["db"]["password"]) or die ("Could not connect: " . mysql_error());
mysql_select_db($GLOBALS['config']["db"]["database"]);

$sql = "UPDATE clientes SET estado='done' WHERE id_utilizador='$id_cliente'";
$result = mysql_query($sql);

mysql_close();
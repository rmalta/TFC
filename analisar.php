<?php
include "config.php";

//analisar se o cliente cancelou ou se ainda esta em espera
error_reporting( E_ALL );

//use this header in order to allow any origin to access the resource
header('Access-Control-Allow-Origin: *');

$id_utilizador = $_REQUEST['nome'];

mysql_connect($GLOBALS['config']["db"]["hostname"], $GLOBALS['config']["db"]["username"], $GLOBALS['config']["db"]["password"]) or die ("Could not connect: " . mysql_error());
mysql_select_db($GLOBALS['config']["db"]["database"]);

$sql = mysql_query("SELECT * FROM clientes WHERE id_utilizador='$id_utilizador'");
error_log("## analisar: ". "SELECT * FROM clientes WHERE id_utilizador='$id_utilizador'", 0);
$array = mysql_fetch_assoc($sql);

echo json_encode(array("estado" => $array['estado']));

mysql_close();
<?php
include "config.php";
//finalizar trajecto do taxista, passar estado de enroute para done
error_reporting( E_ALL );
error_log("end_service.php");
//use this header in order to allow any origin to access the resource
header('Access-Control-Allow-Origin: *');

$username = $_REQUEST['user'];//recebe username do taxista

mysql_connect($GLOBALS['config']["db"]["hostname"], $GLOBALS['config']["db"]["username"], $GLOBALS['config']["db"]["password"]) or die ("Could not connect: " . mysql_error());
mysql_select_db($GLOBALS['config']["db"]["database"]);

//procura utilizador na bd | para taxista
$get_id= mysql_query("SELECT * FROM utilizadores WHERE nome='$username' and id_tipo_utilizador='2'");
$utilizadores= mysql_fetch_assoc($get_id);

$id_utilizador = $utilizadores['id'];//encontra id do utilizador

error_log("end_service.php taxista: " .$id_utilizador , 0);

$sql = "UPDATE taxistas SET disponibilidade='livre', pedidos='no' WHERE id_utilizador=$id_utilizador";
error_log("end_service.php sql: " .$sql , 0);
$result = mysql_query($sql);

mysql_close();
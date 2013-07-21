<?php
include "config.php";
//reencontrar novo taxista
//apaga dados da ultima chamada
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

$query = mysql_query("SELECT * FROM clientes WHERE id_utilizador='$id_utilizador'");
$data = mysql_fetch_assoc($query);

$id_taxista = $data['taxista'];

$update_taxista = mysql_query("UPDATE taxistas SET pedidos='no' WHERE id_utilizador='$id_taxista'");
$update1 = mysql_fetch_assoc($update_taxista);

$update_clientes = mysql_query("UPDATE clientes SET taxista='0' WHERE id_utilizador='$id_utilizador'");
$update2 = mysql_fetch_assoc($update_clientes);

mysql_close();
<?php
//cancelar taxi, limpar dados relativos ao pedido
error_reporting( E_ERROR );

//use this header in order to allow any origin to access the resource
header('Access-Control-Allow-Origin: *');

$nome = $_REQUEST['nome'];

mysql_connect("localhost", "root", "") or die ("Could not connect: " . mysql_error());
mysql_select_db("taxi");

$update_taxista = mysql_query("UPDATE taxistas SET disponibilidade='livre', pedidos='no' WHERE nome='$taxista'");
$update1 = mysql_fetch_assoc($update_taxista);

$update_clientes = mysql_query("UPDATE clientes SET estado='done', taxista='0' WHERE nome='$nome'");
$update2 = mysql_fetch_assoc($update_clientes);

mysql_close();
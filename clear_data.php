<?php
//limpar dados das tabelas
error_reporting( E_ALL );

$nome = $_REQUEST['nome'];

mysql_connect("localhost", "root", "") or die ("Could not connect: " . mysql_error());
mysql_select_db("taxi");
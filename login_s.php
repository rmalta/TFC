<?php
error_reporting( E_ALL );

//use this header in order to allow any origin to access the resource
header('Access-Control-Allow-Origin: *');

session_start();

$username = $_REQUEST['uname'];
$password = $_REQUEST['passwd'];

mysql_connect("localhost", "root", "") or die ("Could not connect: " . mysql_error());
mysql_select_db("taxi");

$sql = "SELECT count(*) FROM utilizadores WHERE( nome='$username' and  password='$password' and id_tipo_utilizador='1')";
 
$query = mysql_query($sql);

$result = mysql_fetch_assoc($query);

//guardar em ficheiro      
$fp = fopen('file2taxi.txt', 'w');
fwrite($fp, print_r($result, TRUE));
fclose($fp);

if($result['count(*)']>0){
    //login concretizado com sucesso
    $_SESSION['username'] = $username;
    echo json_encode(array("result" => "authorized"));
}else{
    //login falhado
    echo json_encode(array("result"=> "not authorized"));
}
 
mysql_close();


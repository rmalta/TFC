<?php
error_reporting( E_ALL );
include "authentication.php";
header('Content-type: application/json');

if($_POST){
	
	if(isset($_POST['token']) && isValidToken($_POST['token'])){
		echo json_encode(array("result"=> "authorized", "token" => $_POST['token']));
		return;
	}
	else if(isset($_POST['uname']) && isset($_POST['passwd']) && isValidAuth($_POST['uname'], $_POST['passwd'])){
		echo json_encode(array("result" => "authorized", "token" => $_SESSION[$_POST['uname']]));
		return;
	}
	
}

echo json_encode(array("result"=> "not authorized"));

?>

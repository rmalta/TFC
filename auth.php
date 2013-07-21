<?php
	header('Content-Type: application/json');
	header('Access-Control-Allow-Origin: *');

	// verifica que os parametros são bem recebidos
	if(isset($_REQUEST['username']) && isset($_REQUEST['password'])){

		if(!isset($_SESSION[$_REQUEST['username']]) ){
			$_SESSION[$_REQUEST['username']] = md5($_REQUEST['password']);
		}

		echo json_encode(array("token" => $_SESSION[$_REQUEST['username']]));


	}else{
		header("HTTP/1.1 500 Internal Server Error");
		echo json_encode(array("description" => "invalid parameters"));
	}

?>

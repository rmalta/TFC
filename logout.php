<?php
	session_start("taxi");
	
	if(isset($_POST['uname']) && isset($_SESSION[$_POST['uname']])){

		unset($_SESSION[$_POST['uname']]);
		echo json_encode(array("result"=> "loged out"));
		return;
	}
	
	echo json_encode(array("result"=> "not loged in"));
?>

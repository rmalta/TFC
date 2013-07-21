<?php

include "authentication.php";
include "config.php";

if(!isset($_POST['token']) || !isValidToken($_POST['token'])){
	error_log("Nao funcionout")
	header("Location: " . $GLOBALS['config']['base_url']);
	exit;
}

?>

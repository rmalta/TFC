<?php

include "authentication.php";
header('Content-type: application/json');

if($_POST){

        if(isset($_POST['uname']) && isset($_POST['passwd']) && isValidFacebookAuth($_POST['uname'], $_POST['passwd'])){
          
	      echo json_encode(array("result" => "authorized", "token" => $_SESSION[$_POST['uname']]));
              return;
        }

}

echo json_encode(array("result"=> "not authorized"));

?>


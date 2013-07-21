<?php

include "config.php";
session_start("taxi");

function isValidFacebookAuth($uname, $token){

	$valid = false;

	$mysqli = new mysqli($GLOBALS['config']["db"]["hostname"], $GLOBALS['config']["db"]["username"], $GLOBALS['config']['db']["password"], $GLOBALS['config']['db']["database"]);

	if($q = $mysqli->prepare("select nome, token from utilizadores where nome = ? and password = _facebook_ and token = ? limit 1")){
        
        	$q->bind_param('ss', $uname, $token);
        
	        $q->execute();
        
        	$q->bind_result($u, $t);
        
	        while($q->fetch()){
        	        $valid = true;
                	$token = $t;
	                break;
        	}
        
        	$q->close();
	}

	$mysqli->close();


	if(!$valid){

		$mysqli = new mysqli($GLOBALS['config']["db"]["hostname"], $GLOBALS['config']["db"]["username"], $GLOBALS['config']['db']["password"], $GLOBALS['config']['db']["database"]);
		
		if($q = $mysqli->prepare("delete from utilizadores where where nome = ? and password = _facebook_")){
			
       		        $q->bind_param('s', $uname);

               		$q->execute();

                	$q->close();
        	}
	
	        if($q = $mysqli->prepare("insert into utilizadores (id_tipo_utilizador, nome,  password, token) values(1, ?, ?, ?)")){
			
			$passwd_fb = "_facebook_";

       		        $q->bind_param('sss', $uname, $passwd_fb, $token);

               		$q->execute();

                	$q->close();

			$valid = true;
        	}

		$mysqli->close();

		$_SESSION[$uname] = $token;
	}

	return $valid;
}

function isValidAuth($uname, $passwd){
        $token = null;
        $valid = false;
        
        $mysqli = new mysqli($GLOBALS['config']["db"]["hostname"], $GLOBALS['config']["db"]["username"], $GLOBALS['config']['db']["password"], $GLOBALS['config']['db']["database"]);
        
        if($q = $mysqli->prepare("select nome, token from utilizadores where nome = ? and password = ? limit 1")){
                
                $q->bind_param('ss', $uname, $passwd);
                
                $q->execute();
                
                $q->bind_result($u, $t);
               
                while($q->fetch()){
                        $valid = true;
                        $token = $t;
			break;
                }
                
                $q->close();
        }
        
        $mysqli->close();
        
        if($valid){
                
		error_log(isset($_SESSION[$uname]), 0);

		if(!isset($_SESSION[$uname]) || $_SESSION[$uname] == ""){
                
		        $_SESSION[$uname] = md5($uname.$passwd.substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10));


			$mysqli = new mysqli($GLOBALS['config']["db"]["hostname"], $GLOBALS['config']["db"]["username"], $GLOBALS['config']['db']["password"], $GLOBALS['config']['db']["database"]);
			if($q = $mysqli->prepare("update utilizadores set token = ? where nome = ? and password = ?")){

				$q->bind_param('sss', $_SESSION[$uname], $uname, $passwd);
				
				$q->execute();

				$q->close();
			}

			$mysqli->close();	
		}
                else{
                        $_SESSION[$uname] = $token;
		}
        }

        return $valid;
}

function isValidToken($token){

        $valid = false;

        $mysqli = new mysqli($GLOBALS['config']["db"]["hostname"], $GLOBALS['config']["db"]["username"], $GLOBALS['config']["db"]["password"], $GLOBALS['config']['db']["database"]);

        if($q = $mysqli->prepare("select nome, token from utilizadores where nome = ? and password = ? limit 1")){

                $q->bind_param('ss', $uname, $passwd);

                $q->execute();

                $q->bind_result($u, $t);

                while($q->fetch()){
                        $valid = true;
                }

                $q->close();
        }

        $mysqli->close();

        return $valid;
}

?>

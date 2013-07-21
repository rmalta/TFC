<?php
include "config.php";
error_reporting( E_ERROR );

//use this header in order to allow any origin to access the resource
header('Access-Control-Allow-Origin: *');

//guardar na base de dados posição do taxista de 10 em 10s e verfica pedidos

$lat = $_REQUEST['lat'];
$lon = $_REQUEST['lon']; 
$username = $_REQUEST['user'];

mysql_connect($GLOBALS['config']["db"]["hostname"], $GLOBALS['config']["db"]["username"], $GLOBALS['config']["db"]["password"]) or die ("Could not connect: " . mysql_error());
mysql_select_db($GLOBALS['config']["db"]["database"]);

//procura utilizador na bd | para taxista
$get_id= mysql_query("SELECT * FROM utilizadores WHERE nome='$username' and id_tipo_utilizador='2'");
$utilizadores= mysql_fetch_assoc($get_id);

$id_utilizador = $utilizadores['id'];//encontra id do utilizador

$taxista_existe = mysql_query("select count(1) as total from taxistas where id_utilizador = '$id_utilizador'");
$rows_array = mysql_fetch_assoc($taxista_existe);
$rows = $rows_array["total"];

//error_log("pos.php Utilizador existe: " .$rows , 0);
if($rows > 0){
    
    //error_log("pos.php taxista: " .$id_utilizador , 0);

    //actualizar taxista para waiting(em espera)
    $result = mysql_query("UPDATE taxistas SET lat='$lat', lon='$lon' WHERE id_utilizador='$id_utilizador'");
    
    
}else{
    //error_log("pos.php Novo taxista: " .$id_utilizador , 0);
    
    //insere taxista para waiting(em espera)
    $result = mysql_query("INSERT INTO taxistas (lat, lon, id_utilizador, disponibilidade, pedidos) VALUES ('$lat','$lon', '$id_utilizador', 'livre', 'no')");    
}

$query2= mysql_query("SELECT * FROM taxistas WHERE id_utilizador='$id_utilizador'");
$array2 = mysql_fetch_assoc($query2);

$disponibilidade = $array2['disponibilidade'];

if($disponibilidade == "livre"){//se taxista está livre
    //verifica se tem pedidos
    if($array2 && $array2['pedidos'] != "no"){//se tem cliente
        //verifica qual o cliente
        $id_cliente = $array2['pedidos'];
        $q_estado = mysql_query("SELECT * FROM clientes WHERE id_utilizador='$id_cliente'");
        $estado = mysql_fetch_assoc($q_estado);
        
        //guardar em ficheiro      
        //$fp = fopen('file2taxi.txt', 'w');
        //fwrite($fp, print_r($estado, TRUE));
        //fclose($fp);
        
        if($estado && $estado['estado'] == "waiting"){//se esse cliente ainda não foi atendido
          //se tem pedidos envia notificação ao taxista         
          $arr['pedidos'] = array ( 'estado'=>'sim' );
          $arr['cliente'] = $estado;
          
           ////guardar em ficheiro      
           // $fp = fopen('file2taxi.txt', 'w');
           // fwrite($fp, print_r($arr, TRUE));
           // fclose($fp);
           //
          echo json_encode($arr);
          
        } else{
            $arr = array ('pedidos'=>'pedido perdido');
            echo json_encode($arr);
        }
    }
    else{//senao tem notificações envia "no" de volta
        $arr = array ( 'pedidos'=>'no' );
        echo json_encode($arr);
    }
}else{
    $arr = array ('pedidos'=>'em servico');
    echo json_encode($arr);
}
mysql_close();
<?php

/*BDD*/
$bdd="prod_carte";
$destination="public";
$host="localhost";
$user="postgres";
$port="5432";
$dbpass="test";

$user_appli="user_prod";
$pass_appli="pass_prod";

$connexion_aesn = "host=".$host." user=".$user_appli." port=".$port." dbname=".$bdd." password=".$pass_appli;	


$server = "";
$input = "C:/wamp/www/prod_carte/data";
$output = "C:/wamp/www/prod_carte/cartes";

$URL_qgisserver = "http://localhost/qgis/qgis_mapserv.fcgi.exe?SERVICE=WMS&VERSION=1.3.0&REQUEST=GetPrint&map=$input/";
$projection = "2154";

?>
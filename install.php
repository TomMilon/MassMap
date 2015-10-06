<?php
//------------------------------------------------------------------------------//
//  install.php                                                                  //
//                                                                              //
//  Version 1.00  13/07/12 - OlGa (CBNMED)                                      //
//------------------------------------------------------------------------------//
// ------------------------------------------------------------------------------ INIT.
require_once('_INCLUDE/fonctions.inc.php');

/*installation*/
if (file_exists("./_INCLUDE/.sql_config.inc.php"))
	header("Location: ./index.php");

?>
<script type="text/javascript" language="javascript" >
$(document).ready(function(){
	$("#form1").validate({
		rules: {
			host: {
				required: true,
                minlength: 2
			},
			port: {
				required: true,
                minlength: 3
			},
			user: {
				required: true,
                minlength: 2
			},
			mdp: {
				required: true,	
                minlength: 3
			},
		},
		messages: {
			host: { required: "",	minlength: ""},
			port: { required: "",	minlength: ""},
			user: { required: "",	minlength: ""},
			mdp: { required: "",	minlength: ""}
		}
	}); 
	
	$("#install-button")
        .button({
            text: true
        });
	
	$("#install-finish-button")
        .button({
            text: true
        })                
        .click(function() {
              window.location.replace ('prod_carte/install.php');
		});
	
    $("#return-button")
        .button({
            text: true
        })                
        .click(function() {
              window.location.replace ('prod_carte/install.php');
		});
	
});
</script> 
<?php

/*--------------------------------------------------------------------------------------------------- EN-TETE */
echo entete();

/*--------------------------------------------------------------------------------------------------- ONGLETS */	
echo("
<div id='entete'>
	<div id ='intro'>
	Application web pour la production de cartes en masse
	</div>
</div>
<div id='main'><div id ='titre'>Tableau de bord</div>"
);

/*-------------------------------------------------------------------------------------*/
/*Variables----------------------------------------------------------------------------*/
if (isset($_POST['action'])) $action = $_POST['action']; else $action = 'install-param';

switch ($action)
{
/*-------------------------------------------------------------------------------------*/
/*Formulaire d'installation------------------------------------------------------------*/
default :
case "install-param":	{	
	echo ("<div id=\"fiche\" >");
	echo ("<form method=\"POST\" id=\"form1\" class=\"form1\" name=\"edit\" action=\"\" >");
	echo ("<center><input type=\"hidden\" name=\"action\" value=\"install-set\" />");
	if (!file_exists("./_INCLUDE/.sql_config.inc.php"))
		{
		require_once ("./_INCLUDE/.sql_config.inc.example.php");
		//------------------------------------------------------------------------------ EDIT LR GRP1
				echo ("<div id=\"radio1\">");    
				echo ("<fieldset style=\"width: 50%;\"><LEGEND>Connexion au serveur de base de données</LEGEND>");
						echo ("<table border=0 width=\"100%\"><tr valign=top >");
						echo ("<td style=\"width: 800px;\">");
							metaform_text ("Hôte","",50,"","host",$host);
							metaform_text ("Port","",25,"","port",$port);
							metaform_text ("Utilisateur admin","",50,"","user",$user);
							metaform_pw ("Mot de passe admin","",50,"","dbpass",$dbpass);
						echo ("</td></tr></table>");	
					echo ("</fieldset>");
		//------------------------------------------------------------------------------ EDIT LR GRP2 
				echo ("<fieldset  style=\"width: 50%;\"><LEGEND> Création de la base de données </LEGEND>");
						echo ("<table border=0 width=\"100%\"><tr valign=top >");
						echo ("<td style=\"width: 800px;\">");
							metaform_text ("Nom de la base","",50,"","bdd",$bdd);
							metaform_text ("Utilisateur Appli","",50,"","user_appli",$user_appli);
							metaform_pw ("Mot de passe Appli","",20,"","pass_appli",$pass_appli);
						echo ("</td></tr></table>");	
					echo ("</fieldset>");
		}
		echo ("</div>");
		echo ("</div></center>");
		echo ("<center><button id=\"install-button\">Lancer l'installation</button></center>");
	echo ("</form>");
	}
	break;

/*-------------------------------------------------------------------------------------*/
/*Réalisation de l'installation--------------------------------------------------------*/
case "install-set":	{
	echo ("<div id=\"fiche\" >");
	if (isset($_POST["host"]) AND isset($_POST["port"]) AND isset($_POST["user"]) AND isset($_POST["dbpass"]) AND isset($_POST["bdd"]) AND isset($_POST["user_appli"]) AND isset($_POST["pass_appli"]))
		{$host = $_POST["host"];$port = $_POST["port"];$user = $_POST["user"];$dbpass = $_POST["dbpass"];$bdd = $_POST["bdd"];$user_appli = $_POST["user_appli"];$pass_appli = $_POST["pass_appli"];}
	elseif (file_exists("./_INCLUDE/.sql_config.inc.php"))
		{require_once ("./_INCLUDE/.sql_config.inc.php");}
	else
		{echo ("Problème de connexion<button id=\"return-button\">retour au menu</button></center>");}
	
	$conn_admin = connexion ($host,$port,$user,$dbpass,"postgres");

	if ($conn_admin)
		{
		echo ("connexion établie<BR>"); 
		
		/*------------------*/
		/*Création de la BDD*/
		$result = pg_query($conn_admin,"SELECT 1 FROM pg_database WHERE datname = '$bdd';");
		$bd_test = pg_fetch_row($result); 
		if ($bd_test[0] == null)
			{
			$result = pg_query($conn_admin,"CREATE DATABASE $bdd ENCODING = 'UTF8' LC_COLLATE = 'French_France.1252' LC_CTYPE = 'French_France.1252';");
			echo ("La base de données $bdd a été créée<BR>"); 
			}
		else
			echo ("La base de données $bdd existait déjà<BR>"); 
		
		/*-------------------------------*/
		/*Création de l'utilisateur CODEX*/
		$conn_appli = connexion ($host,$port,$user,$dbpass,$bdd);
		$result = pg_query($conn_appli,"CREATE extension postgis;");
		$result = pg_query($conn_appli,"SELECT 1 FROM pg_roles WHERE rolname='$user_appli';");
		$user_test = pg_fetch_row($result); 
		if ($user_test[0] == null)		
			{
			$result = pg_query($conn_appli,"CREATE USER $user_appli PASSWORD '$pass_appli';");
			echo ("L'utilisateur $user_appli a été créé<BR>"); 
			}
		else
			echo ("L'utilisateur $user_appli existait déjà<BR>"); 
		
		/*-------------------*/	
		/*Structure de la BDD*/
		$result = pg_query($conn_appli,"SELECT 1 FROM information_schema.tables WHERE table_name='carte_indigenat';");
		$table = pg_fetch_row($result);
		if ($table[0] == null)
			{
			$conn_user = connexion ($host,$port,$user_appli,$pass_appli,$bdd);
			$archi = "./sql/archi_prod_carte.sql";
			$query = create_query($archi,$user_appli);
			$result = pg_query($conn_user,$query);
			echo ("L'architecture de la base a été implémentée<BR>"); 
			}
		else
			echo ("L'architecture de la base existait déjà<BR>"); 


			
		/*------------------------------------------*/
		/*parametrage du ficher de conf sql_connect*/
		if (!file_exists("./_INCLUDE/.sql_config.inc.php"))
			{
			$sql_file = file_get_contents("./_INCLUDE/.sql_config.inc.example.php");
			$sql_file = str_replace("localhost",$_POST["host"],$sql_file);
			$sql_file = str_replace("5432",$_POST["port"],$sql_file);
			$sql_file = str_replace("user_prod",$_POST["user_appli"],$sql_file);
			$sql_file = str_replace("pass_prod",$_POST["pass_appli"],$sql_file);
			$sql_file = str_replace("postgres",$_POST["user"],$sql_file);
			$sql_file = str_replace("test",$_POST["dbpass"],$sql_file);
			$sql_file = str_replace("appli",$_POST["bdd"],$sql_file);
			file_put_contents("./_INCLUDE/.sql_config.inc.php",$sql_file);
			}
	
		
		
		/*Bouton finalisation de l'install*/
		echo ("<form method=\"POST\" id=\"form1\" class=\"form1\" name=\"edit\" action=\"\" >");			
		echo ("<input type=\"hidden\" name=\"action\" value=\"install-finish\" />");
		echo ("<input type=\"hidden\" name=\"host\" value=\"$host\" />");
		echo ("<input type=\"hidden\" name=\"port\" value=\"$port\" />");
		echo ("<input type=\"hidden\" name=\"user\" value=\"$user\" />");
		echo ("<input type=\"hidden\" name=\"dbpass\" value=\"$dbpass\" />");
		echo ("<input type=\"hidden\" name=\"bdd\" value=\"$bdd\" />");
		echo ("<input type=\"hidden\" name=\"user_appli\" value=\"$user_appli\" />");
		echo ("<input type=\"hidden\" name=\"pass_appli\" value=\"$pass_appli\" />");
		echo ("<center><button id=\"install-finish-button\">Fin de l'installation</button></center>");
		echo ("</form>");
		// echo ("<center><button id=\"return-button\">Retour à  l'installation</button></center>");
		}
	else
		{
		echo ("Problèmes de connexion<BR>");
		echo ("<center><button id=\"return-button\">retour au menu</button></center>");
		}
	echo ("</div>");
	}
	break;	

case "install-finish":	{
	header("Location: ./index.php");

	}
	break;
}


function connexion ($host,$port,$user,$dbpass,$bdd) {
	$connexion = "host=$host port=$port user=$user password=$dbpass dbname=$bdd";
	$conn = pg_connect($connexion);
	return $conn;
	}

function create_query($sql,$user_appli) {
	$query = file_get_contents($sql);
	$query = str_replace("pg_user",$user_appli,$query);
	$query = str_replace("postgres",$user_appli,$query);
	return $query;
}	
?>
	
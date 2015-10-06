<?php 
//------------------------------------------------------------------------------//
//  index.php                                                    			//
//                                                                              //
//  Application WEB 'prod_carte'                                                //
//  Ce script pilote l'interface de l'outil								 		//
//                                                                              //
//  Version 1.00  05/10/2015 - FCBN		                                        //
//------------------------------------------------------------------------------//
// ------------------------------------------------------------------------------ INIT.
session_start(); 
set_time_limit(5000);

/*installation*/
if (!file_exists("./_INCLUDE/.sql_config.inc.php"))
	header("Location: ./install.php");
	
include ("_INCLUDE/commun.inc.php");
require_once('_INCLUDE/fonctions.inc.php');



/*--------------------------------------------------------------------------------------------------- VAR. */
if (isset($_GET['page'])) $page = $_GET['page']; else $page = 'ptf_accueil';
$tab = array();
$tab[$page] = "class='active'";
if (!isset($tab['ptf_accueil'])) {$tab['ptf_accueil'] = '';}
if (!isset($tab['ptf_action'])) {$tab['ptf_action'] = '';}

/*--------------------------------------------------------------------------------------------------- EN-TETE */
echo entete();

/*--------------------------------------------------------------------------------------------------- ONGLETS */	
echo("
<div id='entete'>
	<div id ='intro'>
	Application web pour la production de cartes en masse
	</div>
	<div id ='onglet'>
		<ul id='tabnav'>
			<li ".$tab['ptf_accueil']."><a href='".$server."index.php?page=ptf_accueil'>Accueil</a></li>
			<li ".$tab['ptf_action']."><a href='".$server."index.php?page=ptf_action'>Production de carte</a></li>
		</ul>
	</div>
</div>
<div id='main'><div id ='titre'>Tableau de bord</div>"
);


switch ($page) {
/*--------------------------------------------------------------------------------------------------- #ONGLET FORMULAIRE DE PRODUCTION */
	case "ptf_action" : {
		echo("
		<TABLE><TR><TD style=\"vertical-align:top\">
		<h1>Selectionnez le projet à générer</h1>
		<div id=\"form\">
		<Form method=\"POST\" action=\"".$_SERVER['PHP_SELF']."?page=ptf_action\">
		<TABLE id = \"prez\">
			<TR>
				<TD>Nom du projet </TD>
				<TD><SELECT name=\"projet\" >
				");
				for ($i=0;$i<count($base_projet);$i++)
					{
					echo "<option value=\"".$base_projet[$i]."\"";
					if(isset($_POST["projet"])) {if($_POST["projet"] == $base_projet[$i]) {echo "selected";}}
					echo ">".$base_projet[$i]."</option>";
					}
				echo("</SELECT>
				</TD>
			</TR>
			<TR>
				<TD>Projection </TD>
				<TD><input type=\"textbox\" name=\"projection\" value=\"2154\"></TD>
			</TR>
			<TR>
				<TD>Lancer la production ---></TD><TD><input type=\"submit\" name=\"bt_ok\" value=\"OK\"></TD>
			</TR>
		</TABLE>
		
		<TABLE id = 'prez'>
			<TR>
				<TD><h1>Réinitialiser les cartes </h1><input type=\"submit\" name=\"bt_ok\" value=\"Flush\"></TD>
				<TD><h1>Mise à jour des données </h1><input type=\"submit\" name=\"bt_ok\" value=\"maj\"></TD>
			</TR>
		</TABLE>
		
		</form>
		</div>
		</TD></TR></TABLE>
		");


		/*Lancement de la production test = dans le cas où le formulaire est envoyé - bt_ok isset*/
		if (isset($_POST["bt_ok"]))
			{
			/*CAS DE PRODUCTION DES CARTE*/			
			if ($_POST["bt_ok"] == "OK")
				{ 
				if (isset($_POST["projet"]) AND isset($_POST["projection"]))
					{
					$mode = 'html';											/*Le mode HTML est un mode de test pour nous*/
					$projet = $_POST["projet"];
					$projection = $_POST["projection"];
					prod_cart($projet,$projection,$mode,$input,$output);	/*Toute la gestion de la production de la carte est géré dans cette fonction*/
					}
				}	
			/*CAS DE NETTOYAGE DU REPERTOIRE DE PRODUCTION DES CARTES*/	
			if ($_POST["bt_ok"] == "Flush")
				nettoyage();
			/*CAS DE LANCEMENT DE MISE A JOUR DES DONNEES*/	
			if ($_POST["bt_ok"] == "maj")
				echo "fonction désactivée";
			}
		}
		break;

/*--------------------------------------------------------------------------------------------------- #ONGLET ACCUEIL */		
	case "ptf_accueil" : {
		echo("
		<div id=\"container\" style=\"width: 100%\">
			Cette application web vous permettra de produire de façon automatique les cartes à partir d'un projet QGIS<BR>
				
			<BR><u>Pour se faire</u>, 
			<li>copier le projet dans le dossier <b>\data\</b>, 
			<li>... a construire..., 
			<li>sélectionnez l'onglet <b>\"Production de carte\"</b>, 
			<li>entrez le nom du projet et la projection 
			<li>et cliquez sur OK
			<BR><BR>  
		</div>");
		}
		break;
	}	

/*--------------------------------------------------------------------------------------------------- PIED DE PAGE */
echo footer();

?>
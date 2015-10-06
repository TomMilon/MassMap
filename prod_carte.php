<?php 
//------------------------------------------------------------------------------//
//  prod_carte.php                                                    			//
//                                                                              //
//  Application WEB 'prod_carte'                                                //
// Ce script permet de lancer la production en masse de carte en ligne 			//
//	de commande 																//
//             																	//
//  1. une requête récupère le nombre de taxon par projet à produire			//
//  2. Vérification des taxons déjà produits									//
//  3. fonction de production de carte.											//
//             																	//
//  Version 1.00  05/10/2015 - FCBN		                                        //
//------------------------------------------------------------------------------//
// ------------------------------------------------------------------------------ INIT.
include ("fonctions.inc.php");
include ("commun.inc.php");

// ------------------------------------------------------------------------------ VAR.
$mode = 'masse';
$flag = 0;

// ------------------------------------------------------------------------------ Core
/* $req_nb_taxon rassemble les requête de récupération du nombre de taxon par projet */
foreach ($req_nb_taxon as $projet => $requete)
	{
	/*récupération de la liste des taxons*/
	$table=query_bdd($requete,1);
	echo "nombre total de taxons : ".$table[0]."\n";
	
	/*test si toutes les cartes ont été produites ou non*/
	$result_verif = verif($projet,$output);
	echo "carte pour le projet".$projet." : ".$result_verif[1]."\n";
	if ($result_verif[1] < $table[0] AND $flag == 0)
		{
		$projet_todo = $projet;
		$flag = 1;
		}
	}
	
echo "projet en cours de production : ".$projet_todo."<BR>";
/*Toute la gestion de la production de la carte est géré dans cette fonction*/
prod_cart($projet_todo,$projection,$mode,$input,$output);
?>
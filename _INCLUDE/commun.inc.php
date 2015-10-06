<?php
//------------------------------------------------------------------------------//
//   commun.inc.php                                                             //
//                                                                              //
//  Application WEB 'prod_carte'                                                //
// Ce script rassemble tous les paramétrages nécessaire à la production des cartes //
//             																	//
//  Version 1.00  05/10/2015 - FCBN		                                        //
//------------------------------------------------------------------------------//                                 
//------------------------------------------------------------------------------ CONFIG du module
include (".sql_config.inc.php");
$nb_carte = 3;
$flag = ""; /*paramètre pour faire varier le serveur qgis-mapserver à utiliser*/

//------------------------------------------------------------------------------ CONSTANTES du module
/*Nom des projets*/
$base_projet = array (
	"carte_lr",
	"carte_indigenat",
	"carte_degnat",
	"carte_eee",
	// "aesn_taxons_commune",
	// "aesn_carte_simplifie",
	"aesn_syntaxon_dpt",
	"aesn_carte_reg"
	);

/*Requête SQL pour récupérer le nombre de taxons pour chaque projet*/
$req_nb_taxon = array (
	"carte_indigenat" =>	"SELECT count(DISTINCT cd_ref_referentiel) FROM public.carte_indigenat WHERE cd_ref_referentiel IS NOT NULL",
	"carte_lr" =>			"SELECT count(DISTINCT cd_ref_referentiel) FROM public.carte_lr WHERE cd_ref_referentiel IS NOT NULL",
	"carte_degnat" =>		"SELECT count(DISTINCT cd_ref_referentiel) FROM public.carte_degnat WHERE cd_ref_referentiel IS NOT NULL",
	"carte_eee" =>			"SELECT count(DISTINCT cd_ref_referentiel) FROM public.carte_eee WHERE cd_ref_referentiel IS NOT NULL",
	// "aesn_taxons_commune" =>		"",
	// "aesn_carte_simplifie" =>	"",
	"aesn_syntaxon_dpt" =>		"SELECT count(DISTINCT id_aesn) FROM public.aesn_syntaxon_dpt WHERE id_aesn IS NOT NULL",
	"aesn_carte_reg" =>			"SELECT count(DISTINCT id_aesn) FROM public.aesn_carte_reg WHERE id_aesn IS NOT NULL"
	);

/*Requête SQL pour récupérer la liste des taxons pour chaque projet*/	
$req_liste_taxon = array (
	"carte_indigenat" =>	"SELECT DISTINCT cd_ref_referentiel, nom_complet_liste FROM public.carte_indigenat WHERE cd_ref_referentiel IS NOT NULL AND cd_ref_referentiel NOT IN",
	"carte_lr" =>			"SELECT DISTINCT cd_ref_referentiel, nom_complet_liste FROM public.carte_lr WHERE cd_ref_referentiel IS NOT NULL AND cd_ref_referentiel NOT IN",
	"carte_degnat" =>		"SELECT DISTINCT cd_ref_referentiel, nom_complet_liste FROM public.carte_degnat WHERE cd_ref_referentiel IS NOT NULL AND cd_ref_referentiel NOT IN",
	"carte_eee" =>			"SELECT DISTINCT cd_ref_referentiel, nom_complet_liste FROM public.carte_eee WHERE cd_ref_referentiel IS NOT NULL AND cd_ref_referentiel NOT IN",
	// "aesn_taxons_commune" =>		"",
	// "aesn_carte_simplifie" =>	"",
	"aesn_syntaxon_dpt" =>		"SELECT DISTINCT id_aesn, lb_syntaxon FROM public.aesn_syntaxon_dpt WHERE id_aesn IS NOT NULL AND id_aesn NOT IN",
	"aesn_carte_reg" =>			"SELECT DISTINCT id_aesn, lb_syntaxon FROM public.aesn_carte_reg WHERE id_aesn IS NOT NULL AND id_aesn NOT IN"
	);

/*Partie de l'URL pour la production de carte - la projection, le modèle de carte, l'extent et la résolution*/		
$source = array (
	"carte_indigenat" =>		"&SRS=EPSG:2154&TEMPLATE=carto&map0:EXTENT=-265232,6019957,1606706,7137123&FORMAT=jpg&DPI=300",
	"carte_lr" =>				"&SRS=EPSG:2154&TEMPLATE=carto&map0:EXTENT=-265232,6019957,1606706,7137123&FORMAT=jpg&DPI=300",
	"carte_degnat" =>			"&SRS=EPSG:2154&TEMPLATE=carto&map0:EXTENT=-265232,6019957,1606706,7137123&FORMAT=jpg&DPI=300",
	"carte_eee" =>				"&SRS=EPSG:2154&TEMPLATE=carto&map0:EXTENT=-265232,6019957,1606706,7137123&FORMAT=jpg&DPI=300",
	// "aesn_taxons_commune" =>	"&SRS=EPSG:2154&TEMPLATE=carto&map0:EXTENT=-265232,6019957,1606706,7137123&FORMAT=jpg&DPI=300",
	// "aesn_carte_simplifie" =>	"&SRS=EPSG:2154&TEMPLATE=carto&map0:EXTENT=320356,6618963,925024,7045072&FORMAT=jpg&DPI=300",
	"aesn_syntaxon_dpt" =>		"&SRS=EPSG:2154&TEMPLATE=carto&map0:EXTENT=320356,6618963,925024,7045072&FORMAT=jpg&DPI=300",
	"aesn_carte_reg" =>			"&SRS=EPSG:2154&TEMPLATE=carto&map0:EXTENT=320356,6618963,925024,7045072&FORMAT=jpg&DPI=300"
	);
		
/*Partie de l'URL pour la production de carte - les couches géographique*/	
$layer = array (
	"carte_indigenat" =>		"&LAYER=test_indigenat,indigenat",
	"carte_lr" =>				"&LAYER=test_lr,LR_regionales",
	"carte_degnat" =>			"&LAYER=test_degnat,degre_naturalisation,regions_biogeo_aee_france",
	"carte_eee" =>				"&LAYER=statut_eee,test_eee,regions_biogeo_aee_france",
	// "aesn_taxons_commune" =>	"&LAYER=villes_agence_seine_normandie_RGF93,aesn_repartition_commune,regions_agence_seine_normandie,cours_eau_pcp_aesn,contours_AESN_l93,regions_biogeo_aee_france,european_countries_WGS84,fond_mer",
	// "aesn_carte_simplifie" =>	"&LAYER=fond_mer,european_countries_WGS84,regio_biogeo_aesn,contours_AESN_l93,regions_agence_seine_normandie,aesn_presence_lorraine,departement_aesn,cours_eau_niveau_2,aesn_carte_simplifie,villes_agence_seine_normandie_RGF93,aesn_liste_taxons",
	"aesn_syntaxon_dpt" =>		"&LAYER=fond_mer,european_countries_WGS84,regio_biogeo_aesn,contours_AESN_l93,regions_agence_seine_normandie,departement_aesn,aesn_syntaxon_dpt,cours_eau_niveau_2,villes_agence_seine_normandie_RGF93,aesn_syntaxon",
	"aesn_carte_reg" =>			"&LAYER=fond_mer,european_countries_WGS84,regio_biogeo_aesn,contours_AESN_l93,regions_agence_seine_normandie,departement_aesn,aesn_carte_reg,cours_eau_niveau_2,villes_agence_seine_normandie_RGF93,aesn_syntaxon"
	);

/*nomenclature pour les fichiers de sortie*/		
$short = array (
	"carte_indigenat" =>		"indigenat",
	"carte_lr" =>				"lr",
	"carte_degnat" =>			"degnat",
	"carte_eee" =>				"eee",
	// "aesn_taxons_commune" =>	"aesn_taxon_commune",
	// "aesn_carte_simplifie" =>	"aesn_repartition_taxon",
	"aesn_syntaxon_dpt" =>		"aesn_repartition_syntaxon",
	"aesn_carte_reg" =>			"aesn_repartition_syntaxon"
	);

?>

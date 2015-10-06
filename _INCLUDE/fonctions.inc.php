<?php
//------------------------------------------------------------------------------//
//  fonction.inc.php                                                    		//
//                                                                              //
//  Application WEB 'prod_carte'                                                //
//  Ce script rassemble toutes les fonctions							 		//
//                                                                              //
//  Version 1.00  05/10/2015 - FCBN		                                        //
//------------------------------------------------------------------------------//
// ------------------------------------------------------------------------------ INIT.
	
function entete () 
	{
	/*FONCTION En tête de l'interface*/
	$header = "
	<html>
	<head>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
		<title>Production de carte automatique avec QGIS Server</title>
		<link rel=\"shortcut icon\" href=\"image/FEDERATION_WEB.ico\" type=\"image/x-icon\"/>
		<link rel=\"icon\" href=\"image/FEDERATION_WEB.ico\" type=\"image/x-icon\"/>
		<link rel=\"stylesheet\" media=\"screen\" type=\"text/css\" href=\"interface.css\" />
	</head>
	<body>
	";
	return $header;
	}

function footer () 
	{
	/*FONCTION Pied de page de l'interface*/
	$footer = "
		</div>
		<div id='bottom'>
			<img src=\"image/FEDERATION_WEB.jpg\">
			© Fédération des Conservatoires Botaniques Nationaux - données provenant du réseau des CBN
		</div>
		</body>
	</html>
	";
	return $footer;
	}
		
function prod_cart($projet,$projection,$mode,$nb_carte)
	{
	/*FONCTION DE PRODUCTION DE CARTE EN MASSE*/
	include ("_INCLUDE/commun.inc.php");

	if (securite($projet) == "ok")
		{
		/*création ou test du fichier de log*/
		$result_verif = verif($projet,$output);
		
		/*Affichage*/
		if ($mode == "html") echo "nb carte produites : ".$result_verif[1]."<BR>";	else echo "";
		if ($mode == "html") echo "PROJET : ".$projet."<BR>";	else echo "";
		
		/* $req_liste_taxon récupère la liste de taxon en fonction du projet*/
		$query=$req_liste_taxon[$projet]." ".$result_verif[0];
		echo "$query<BR>";
		$reponse=query_bdd($query,2);
			$table=$reponse[0];
			$nom=$reponse[1];
	
		/*Construction de l'URL utilisé pour produire la carte*/
		/*$URL initialisé dans .sql_config.inc*/
		$URL = $URL_qgisserver;
		$URL .= "$projet.out.qgs";
		$URL .= $source[$projet].$layer[$projet];
		
		/*Affichage*/
		if ($mode == "html") $nb_carte_prod = $nb_carte; else $nb_carte_prod = count($table);
		
		if (!empty($table[0]))
			{
			for ($i=0;$i<$nb_carte_prod;$i++)
				{
				/*Création du nouveau fichier projet avec le taxon à interroger*/
				$file = file_get_contents("$input/$projet.qgs");
				$texte = str_replace("100024",$table[$i],$file);
				file_put_contents("$input/$projet.out.qgs",$texte);

				/*Soucis d'encodage*/
				$espece = nettoyerChaine($nom[$i]);
				$espece  = utf8_decode($espece);

				/*Affichage*/
				if ($mode == "html") echo "$table[$i]<BR>";	else echo "Taxon $table[$i] \n ";
				if ($mode == "html") echo "$URL<BR>";	else echo "$URL\n";
				
				/*Enregistrement de l'image*/
				if (file_get_contents($URL))
					{
					$taxprod = "ok";
					$img = file_get_contents($URL);
					file_put_contents("$output/$projet/".$espece."_".$short[$projet]."_".$table[$i].".jpg",$img);
					imagethumb("$output/$projet/".$espece."_".$short[$projet]."_".$table[$i].".jpg","$output/$projet/".$espece."_".$short[$projet]."_thumb_".$table[$i].".jpg",400);
					} 
					else $taxprod = "no";
					
				/*Nettoyage et log*/
				unlink("$input/$projet.out.qgs");
				if ($taxprod == "ok")
					file_put_contents("$output/$projet/cartes_taxons_produit_$projet.txt","'$table[$i]'\n",FILE_APPEND);

				/*activation affichage*/
				$buffer = strlen($table[$i])+3;
				flush_buffers($buffer);
				}
			}
		}
	}

function verif($projet,$output)
	{
	/*FONCTION Vérifiaction des taxons produits*/
	/*Si aucun répertoire n'existe, le créer*/
	if (!is_dir("$output/$projet"))
		mkdir("$output/$projet");
	
	/*Si aucun fichier de log, le créer*/
	if (!file_exists("$output/$projet/cartes_taxons_produit_$projet.txt"))
		file_put_contents("$output/$projet/cartes_taxons_produit_$projet.txt","");
	
	/*récupération du contenu du log*/
	$taxons_produits = file("$output/$projet/cartes_taxons_produit_$projet.txt");
	
	/*récupération des valeurs d'intérêt*/
	if (empty($taxons_produits))
		$liste_taxons_produits = "('valeur nulle')";
	else
		{
		$liste_taxons_produits = implode(',',$taxons_produits);
		$liste_taxons_produits = "(".$liste_taxons_produits.")";
		}
	
	$nb_produite = count($taxons_produits);
	
	$result = array(
		0 => $liste_taxons_produits,
		1 => $nb_produite
		);
	
	return $result;
	}

function query_bdd($query,$nb_attribut = 1)
	{
	/*FONCTION CONNEXION BDD ET ENVOIE REQUETE */
	include ".sql_config.inc.php";
	$flag = 0;

	/*Connexion et requetage*/
	$dbconn= pg_connect($connexion_aesn);
	$resultat = pg_query($query);

	/*Récupération du résultat*/
	if ($nb_attribut == 1)
		{
		while ($ligne = pg_fetch_row($resultat))
			{
			$table[$flag] = $ligne[0];
			$flag = $flag + 1;
			}
		}
	else
		{
		while ($ligne = pg_fetch_row($resultat))
			{
			for ($i=0 ; $i<=$nb_attribut-1 ; $i++)
				{
				$table[$i][$flag] = $ligne[$i];
				}
			$flag = $flag + 1;
			}
		}

	if(!isset($table))	$table = array();

	/*Fermeture de la connexion*/
	pg_close($dbconn);
		
	return $table;
	}	

function nettoyage()
	{
	/*FONCTION DE NETTOYAGE DE REPERTOIRE DE PRODUCTION*/
	include ".sql_config.inc.php";
	$projet = $_POST["projet"];
		
	$files = scandir("$output/$projet");
	var_dump($files);
	For ($i=2;$i<count($files);$i++)	/*NB : commence à 2 car $files[0] = ./ et $files[1] = ../ */
		{
		unlink("$output/$projet/$files[$i]");
		}
	}

function flush_buffers($buffer)
	{
    ob_start(null,$buffer);
	ob_flush();
	flush(); 
	ob_end_flush();
	}

function imagethumb( $image_src , $image_dest = NULL , $max_size = 100, $expand = FALSE, $square = FALSE )
	{
	/*FONCTION DE PRODUCTION DE VIGNETTE*/
	    if( !file_exists($image_src) ) return FALSE;
	 
	    // Récupère les infos de l'image
	    $fileinfo = getimagesize($image_src);
	    if( !$fileinfo ) return FALSE;
	 
	    $width     = $fileinfo[0];
	    $height    = $fileinfo[1];
	    $type_mime = $fileinfo['mime'];
	    $type      = str_replace('image/', '', $type_mime);
	 
	    if( !$expand && max($width, $height)<=$max_size && (!$square || ($square && $width==$height) ) )
	    {
	        // L'image est plus petite que max_size
	        if($image_dest)
	        {
	            return copy($image_src, $image_dest);
	        }
	        else
	        {
	            header('Content-Type: '. $type_mime);
	            return (boolean) readfile($image_src);
	        }
	    }
	 
	    // Calcule les nouvelles dimensions
	    $ratio = $width / $height;
	 
	    if( $square )
	    {
	        $new_width = $new_height = $max_size;
	 
	        if( $ratio > 1 )
	        {
	            // Paysage
	            $src_y = 0;
	            $src_x = round( ($width - $height) / 2 );
	 
	            $src_w = $src_h = $height;
	        }
	        else
	        {
	            // Portrait
	            $src_x = 0;
	            $src_y = round( ($height - $width) / 2 );
	 
	            $src_w = $src_h = $width;
	        }
	    }
	    else
	    {
	        $src_x = $src_y = 0;
	        $src_w = $width;
	        $src_h = $height;
	 
	        if ( $ratio > 1 )
	        {
	            // Paysage
	            $new_width  = $max_size;
	            $new_height = round( $max_size / $ratio );
	        }
	        else
	        {
	            // Portrait
	            $new_height = $max_size;
	            $new_width  = round( $max_size * $ratio );
	        }
	    }
	 
	    // Ouvre l'image originale
	    $func = 'imagecreatefrom' . $type;
	    if( !function_exists($func) ) return FALSE;
	 
	    $image_src = $func($image_src);
	    $new_image = imagecreatetruecolor($new_width,$new_height);
	 
	    // Gestion de la transparence pour les png
	    if( $type=='png' )
	    {
	        imagealphablending($new_image,false);
	        if( function_exists('imagesavealpha') )
	            imagesavealpha($new_image,true);
	    }
	 
	    // Gestion de la transparence pour les gif
	    elseif( $type=='gif' && imagecolortransparent($image_src)>=0 )
	    {
	        $transparent_index = imagecolortransparent($image_src);
	        $transparent_color = imagecolorsforindex($image_src, $transparent_index);
	        $transparent_index = imagecolorallocate($new_image, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
	        imagefill($new_image, 0, 0, $transparent_index);
	        imagecolortransparent($new_image, $transparent_index);
	    }
	 
	    // Redimensionnement de l'image
	    imagecopyresampled(
	        $new_image, $image_src,
	        0, 0, $src_x, $src_y,
	        $new_width, $new_height, $src_w, $src_h
	    );
	 
	    // Enregistrement de l'image
	    $func = 'image'. $type;
	    if($image_dest)
	    {
	        $func($new_image, $image_dest);
	    }
	    else
	    {
	        header('Content-Type: '. $type_mime);
	        $func($new_image);
	    }
	 
	    // Libération de la mémoire
	    imagedestroy($new_image);
	 
	    return TRUE;
	}
		
function nettoyerChaine($chaine)
	{
	// $caracteres = array(
		// 'À' => 'a', 'Á' => 'a', 'Â' => 'a', 'Ä' => 'a', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', '@' => 'a',
		// 'È' => 'e', 'É' => 'e', 'Ê' => 'e', 'Ë' => 'e', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', '€' => 'e',
		// 'Ì' => 'i', 'Í' => 'i', 'Î' => 'i', 'Ï' => 'i', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
		// 'Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o', 'Ö' => 'o', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o',
		// 'Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'µ' => 'u',
		// 'Œ' => 'oe', 'œ' => 'oe',
		// '$' => 's');
	$caracteres = array(
		'À' => 'a', 'Á' => 'a', 'Â' => 'a', 'Ä' => 'a', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', '@' => 'a',
		'È' => 'e', 'É' => 'e', 'Ê' => 'e', 'Ë' => 'e', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', '€' => 'e',
		'Ì' => 'i', 'Í' => 'i', 'Î' => 'i', 'Ï' => 'i', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
		'Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o', 'Ö' => 'o', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o',
		'Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'µ' => 'u',
		'Œ' => 'oe', 'œ' => 'oe',
		'$' => 's');
 
	$chaine = strtr($chaine, $caracteres);
	// $chaine = preg_replace('#[^A-Za-z0-9]+#', '-', $chaine);
	// $chaine = trim($chaine, '-');
	// $chaine = strtolower($chaine);
 
	return $chaine;
}

function securite ($projet) 
	{
	/*FONCTION TEST DE SECURITE*/
	if (strstr(str_replace("&amp;","",$projet),";") != FALSE)	/*Ne pas introduire de requête SQL dans le formulaire*/
		$result = "mauvaise entrée, merci de respécifier le projet<BR>";
	elseif ($projet == "")	/*Ne pas introduire de champ vide dans le formulaire*/
		$result =  "merci de spécifier le projet<BR>";
	else
		$result = "ok";
	return $result;
	}


function metaform_text ($label,$descr,$long,$style,$champ,$val)
	{
	/*FONCTION CONSTRUCTION DE FORMULAIRE*/	
	if (strpos($descr,"no_lab") == false)
		if (strpos($descr,"bloque") != false) echo ("<label class=\"preField_calc\">".$label."</label>");
		else echo ("<label class=\"preField\">".$label."</label>");

	if (!isset($extra)) $extra = "";		
	// if (strpos($descr,"bloque") != false) {$bloc .= " readonly disabled";$extra .= "background-color:#EFEFEF";}
	if (strpos($descr,"bloque") != false) {$extra .= " disabled class=\"bloque\"";}
	echo ("<input type=\"text\" name=\"".$champ."\" id=\"".$champ."\" size=\"".$long."\" value=\"".$val."\" $extra style=\"$style\"/>");
    echo ("<br>");
	}

function metaform_pw ($label,$descr,$long,$style,$champ,$val)
	{
	/*FONCTION CONSTRUCTION DE FORMULAIRE*/		
	if (strpos($descr,"no_lab") == false)
		if (strpos($descr,"bloque") != false) echo ("<label class=\"preField_calc\">".$label."</label>");
		else echo ("<label class=\"preField\">".$label."</label>");

	if (!isset($extra)) $extra = "";		
	// if (strpos($descr,"bloque") != false) {$bloc .= " readonly disabled";$extra .= "background-color:#EFEFEF";}
	if (strpos($descr,"bloque") != false) {$extra .= " disabled class=\"bloque\"";}
	echo ("<input type=\"password\" name=\"".$champ."\" id=\"".$champ."\" size=\"".$long."\" value=\"".$val."\" $extra style=\"$style\"/>");
    echo ("<br>");
	}

	
	
	?>
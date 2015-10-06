# MassMap
MassMap is an application that allow mass production of map (eg. repartition species map => 1 map by species for 10 000 species)

MassMap est un outil de production de carte en masse. Basé sur QGIS et QGIS-Serveur, il permet d'automatiser la production d'un carte en y associant un jeu de données avec une variable d'entrée. Initialement prévu pour produire des cartes de répartition de taxon flore, il peut être adapté pour d'autres types de carte à condition que ceux ci respecte la règle suivante : une entité = une carte (dans notre cas, un taxon = une carte).

Pré-requis :
- QGIS-Server
- PostgreSQL / PostGIS

Utilisation :
- Suivre le formulaire de connexion pour installer l'outil
- Construire le projet QGIS de votre choix
- Déposer le porjet QGIS et les données dans le dossier DATA
- Paramétrez le fichier commun.inc.php
- Lancer la production à travers l'interface web

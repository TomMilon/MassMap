# MassMap

MassMap is an application that allow mass production of map (eg. repartition species map => 1 map by species for 10 000 species)

MassMap est un outil de production de carte en masse. Basé sur QGIS et QGIS-Serveur, il permet d'automatiser la production d'un carte en y associant un jeu de données avec une variable d'entrée. Initialement prévu pour produire des cartes de répartition de taxon flore, il peut être adapté pour d'autres types de carte à condition que ceux ci respecte la règle suivante : une entité = une carte (dans notre cas, un taxon = une carte).

## Pré-requis :
- Serveur Web supportant le php (testé avec Apache 2.2.22 et php 5.3.10 + php_cli)
- QGIS-Server (testé avec la version 2.2.0-1)
- PostgreSQL (testé avec la version 9.1) / PostGIS (testé avec la version 2.0.1)

## Installation :
- Installer les outils nécessaires (nb : activer les extensions php suivantes php_pgsql et php_pdo_pgsql)
- Une fois que vous êtes sur la page de l'outil, suivre le formulaire de connexion pour installer l'outil (redirection automatique vers localhost/massmap/install.php)

## Utilisation :
Pour créer un projet, il vous faut :
- construire le projet QGIS de votre choix (.qgs),
- déposer le projet QGIS (.qgs) et les données dans le dossier data de massmap,
- charger la liste des taxons à produire dans la base de données,
- paramétrer le fichier commun.inc.php.

Lancer la production à travers l'interface web pour tester la production de quelques cartes
Pour lancer la production en masse, utiliser la commande suivante :
php -f prod_carte.php

[![Join the chat at https://gitter.im/TomMilon/MassMap](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/TomMilon/MassMap?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

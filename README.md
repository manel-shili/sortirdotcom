# SORTIRDOTCOM

Site web de réseau social interne d'une organisation, en l'occurence l'ENI.

C'est un travail de groupe de trois personnes, effectué en collaboration et en méthode AGILE/Scrum, au cours de la formation à l'ENI 2022-23. Il montre ce que nous avons pu entreprendre en PHP Symfony durant le temps imparti juste après avoir eu le cours magistral et pratique.

Travail en cours, ne pas utiliser en production.

![Screenshots-feu](screenshots-feu.gif?raw=true "Screenshot-feu")
![Screenshots-overview](screenshots-overview.gif?raw=true "Screenshot-overview")

# Pré-requis
- Symfony 5.4
- Composer

# Installation

- `git clone https://github.com/Luc-Anne/sortirdotcom.git`
- `cd sortirdotcom`
- `composer install`
- `symfony server:ca:install`
- configurer DATABASE_URL pour cibler une base de données inexistante
- créer la base de données avec votre gestionnaire de BDD ou `symfony console doctrine:database:create`
- `symfony console doctrine:schema:update --force`
- exécuter le fichier sql '/script/SQL/dataSets.sql' sur votre base de données
- `symfony server:start`
- autoriser la lecture automatique de l'audio sur votre navigateur pour une meilleure utilisation du site
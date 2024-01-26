Création des VM avec fonction ssh

Définir le réseau avec acces par pont

user:

root -> password = DatabaseRoot23!

adminuser -> password = Admin23!

VM DB

- Installation de mariadb pour base de données mysql

apt update
apt upgrade
apt install mariadb-server

Se connecter a mariadb avec la commande : mysql
créer un user pour donner accès à la db en remote

GRANT ALL ON *.* TO 'appuser'@'0.0.0.0' IDENTIFIED BY 'password' WITH GRANT OPTION;

VM FRONT

Modifié la connection à la DB avec user appuser et ip du serveur DB

installer mariadb-client

apt install mariadb-client

Installer apache2

apt install apache2

installer curl

apt install curl

installer symfony cli

curl -sS https://get.symfony.com/cli/installer | bash

installer composer

curl -sS https://getcomposer.org/installer -o composer-setup.php

cloner le projet avec git

Dans le dossier projet, lancé la commande :

php -S 0.0.0.0:9000 -t public

Se connecter au projet dans le navigateur avec l'ip du serveur front
192.168.143.126:9000/home



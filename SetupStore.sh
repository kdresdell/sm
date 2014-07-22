# !/bin/bash
#
#  Copyright (C) 2014
#  Ken Dresdell
#  All Rights Reserved
#
#
#  Last update 2014-07-20


source lib/lib.sh
##################################################################
#
#
#    PROGRAMME PRINCIPAL
#
#
##################################################################



VERSION="0.1"

#header "Installateur de boutique en ligne"
#title "Mise a jour du OS"
#apt-get update  -qy
#apt-get upgrade -qy

#header "Installateur de boutique en ligne"
#title "Setup SSH-Server on TCP 222"
#apt-get install openssh-server -qy
#sed -i "s/^Port 22$/Port 2222/g" /etc/ssh/sshd_config
#service ssh restart 

#header "Installateur de boutique en ligne"
#title "Installation du Firewall"
#cp -i bin/firewall.sh /etc/
#title "Activation du firewall au reboot"
#chmod +x /etc/firewall.sh
#sed -i '\/etc\/firewall.sh/d' /etc/rc.local
#sed -i -e '$i \/etc/firewall.sh' /etc/rc.local

#header "Installateur de boutique en ligne"
#title "Installation du Serveur Web NGINX"
#apt-get install nginx -qy

#header "Installateur de boutique en ligne"
#title "Installation du Serveur de BD MySQL"
#apt-get install mysql-server -qy
#/mysql_install_db
#mysql_secure_installation

header "Installateur de boutique en ligne"
title "Installation PHP5, ET MODULES"
sudo apt-get install php5-fpm php5-mysql php5-gd libssh2-php -qy
title "Correction du fichier php.ini pathinfo=0"
sed -i "s/^;cgi.fix_pathinfo=1$/cgi.fix_pathinfo=0/g" /etc/php5/fpm/php.ini
echo "<?php
phpinfo();
?>" > /usr/share/nginx/html/test.php


title "Correction du default php"
sed -i "s/index index.html index.htm;$/index index.php index.html index.htm;/g" /etc/nginx/sites-available/default

title "Restart service PHP & NGINX"
service php5-fpm restart
service nginx restart

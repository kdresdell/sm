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

#title "Creation usager MySQl pour la boutique"
#echo -n "MySQL - "
#mysql -u root -p < conf/initdb.sql 


#header "Installateur de boutique en ligne"
#title "Installation PHP5, ET MODULES"
#sudo apt-get install php5-fpm php5-mysql php5-gd libssh2-php -qy
#title "Correction du fichier php.ini pathinfo=0"
#sed -i "s/^;cgi.fix_pathinfo=1$/cgi.fix_pathinfo=0/g" /etc/php5/fpm/php.ini


##
## Backup des fichiers d'origine Nginx nginx.conf default
## copie des fichiers optimisés
##

#title "backup de fichier config NGINX d'origine"
#mv /etc/nginx/nginx.conf /etc/nginx/_nginx.conf.origin
#mv /etc/nginx/sites-available/default /etc/nginx/sites-available/_default.origin


#title "Copie des templates optimises"
#cp conf/nginx/nginx.conf /etc/nginx/
#cp conf/nginx/sites-available/default /etc/nginx/sites-available/

#title "Restart service PHP & NGINX"
#service php5-fpm restart
#service nginx restart


##
## DOWNLOAD DERNIERE VERSION WORDPRESS
##

title "Supression clean html directory"
rm -rf /usr/share/nginx/html/*


title "Download derniere version Wordpress"
mkdir tmp
cd tmp
wget http://wordpress.org/latest.tar.gz
tar xzvf latest.tar.gz
cd wordpress
cp wp-config-sample.php wp-config.php
sed -i "s/database_name_here/wordpress/g" wp-config.php
sed -i "s/username_here/wp_admin/g" wp-config.php
sed -i "s/password_here/wpadmin\&2014/g" wp-config.php
mkdir wp-content/uploads

cd ..
rsync -avP wordpress/ /usr/share/nginx/html/
chown -R www-data:www-data /usr/share/nginx/html/*
chown -R www-data:www-data /usr/share/nginx/html/wp-content/uploads
cd ..
rm -rf tmp



title "Download and copy WooCommerce"
apt-get install unzip -qy
mkdir tmp
cd tmp
wget http://downloads.wordpress.org/plugin/woocommerce.zip
unzip woocommerce.zip 
cp -R woocommerce /usr/share/nginx/html/wp-content/plugins
chown -R www-data:www-data /usr/share/nginx/html/*
cd ..
rm -rf tmp



title "Copie de nos themes WP et plugins"
mkdir tmp
cp wp/Themes/Mecor/mercor.zip tmp/ 
cp wp/Themes/DynamiX-WordPress/DynamiX.zip tmp/
cp wp/Themes/Salient/salient.zip tmp/

cd tmp
unzip mercor.zip
unzip DynamiX.zip
unzip salient.zip

cp -R mercor /usr/share/nginx/html/wp-content/themes
cp -R DynamiX /usr/share/nginx/html/wp-content/themes
cp -R salient /usr/share/nginx/html/wp-content/themes

chown -R www-data:www-data /usr/share/nginx/html/wp-content/*
cd ..
rm -rf tmp


title "Copie de nos plugins maison"
cp -R wp/Plugins/Ngxf2b	/usr/share/nginx/html/wp-content/plugins
chown -R www-data:www-data /usr/share/nginx/html/wp-content/*





##
## CREATION DU SWAP DRIVE (A la fin puisque demande un reboot)
##


# title "Creation du Swap drive de 1024"
# dd if=/dev/zero of=/swapfile bs=1M count=1024
# mkswap /swapfile
# swapon /swapfile
# echo "/swapfile       none    swap    sw      0       0" >> /etc/fstab 
# echo 10 | sudo tee /proc/sys/vm/swappiness
# echo vm.swappiness = 10 | sudo tee -a /etc/sysctl.conf
# sudo chown root:root /swapfile 
# sudo chmod 0600 /swapfile
# swapon -s
# free


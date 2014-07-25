# !/bin/bash
#
#  Copyright (C) 2014
#  Ken Dresdell
#  All Rights Reserved
#
#
#  Last update 2014-07-20



UpdateOS(){
	header "Installateur de boutique en ligne"
	title "Mise a jour du OS"
	apt-get update  -qy
	apt-get upgrade -qy
}

Set_Hostname(){
	header "Installateur de boutique en ligne"
	title "Setup Server Hosname"
	echo -n "Please enter your hostname FQDN :"
	read hostname
    echo $hostname > /etc/hostname
    hostname $hostname
}



Set_SSH(){
	header "Installateur de boutique en ligne"
    title "Setup SSH-Server on TCP 2222"
    apt-get install -qy openssh-server
    sed -i "s/^Port 22$/Port 2222/g" /etc/ssh/sshd_config
    service ssh restart 
}


Set_MySQL(){
	header "Installateur de boutique en ligne"
	title "Installation du Serveur de BD MySQL"
	apt-get install -qqy mysql-server
	/mysql_install_db
	mysql_secure_installation

	title "Creation usager MySQl pour la boutique"
	echo -n "MySQL - "
	mysql -u root -p < conf/initdb.sql  
}


Set_Nginx(){
	header "Installateur de boutique en ligne"
	title "Installation du Serveur Web NGINX"
	apt-get install -qy nginx

	header "Installateur de boutique en ligne"
	title "Installation PHP5 ET MODULES"
	apt-get install -qy php5-fpm php5-mysql php5-gd libssh2-php
	title "Correction du fichier php.ini pathinfo=0"
	sed -i "s/^;cgi.fix_pathinfo=1$/cgi.fix_pathinfo=0/g" /etc/php5/fpm/php.ini

	title "backup des fichiers config NGINX"
	mv /etc/nginx/nginx.conf /etc/nginx/_nginx.conf.origin
	mv /etc/nginx/sites-available/default /etc/nginx/sites-available/_default.origin

	title "Copie des templates optimises"
	cp conf/nginx/nginx.conf /etc/nginx/
	cp conf/nginx/sites-available/default /etc/nginx/sites-available/

	title "Restart service PHP & NGINX"
	service php5-fpm restart
	service nginx restart
}

Set_Wordpress(){
	header "Installateur de boutique en ligne"
	title "Supression html directory"
	rm -rf /usr/share/nginx/html/*

	title "Download latest Wordpress"
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

	title "Install Wordpress"
	cd ..
	rsync -avP wordpress/ /usr/share/nginx/html/
	chown -R www-data:www-data /usr/share/nginx/html/*
	chown -R www-data:www-data /usr/share/nginx/html/wp-content/uploads
	cd ..
	rm -rf tmp
}


Set_Woo(){
	header "Installateur de boutique en ligne"
	title "Download WooCommerce"
	apt-get install -qy unzip
	mkdir tmp
	cd tmp
	wget http://downloads.wordpress.org/plugin/woocommerce.zip
	unzip woocommerce.zip 
	title "Install WooCommerce"
	cp -R woocommerce /usr/share/nginx/html/wp-content/plugins
	chown -R www-data:www-data /usr/share/nginx/html/*
	cd ..
	rm -rf tmp
}


Set_Themes(){
	header "Installateur de boutique en ligne"
	title "Copie themes et plugins maison"
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

	cp -R wp/Plugins/Ngxf2b	/usr/share/nginx/html/wp-content/plugins
	chown -R www-data:www-data /usr/share/nginx/html/wp-content/*
}


Set_Ftp(){
	header "Installateur de boutique en ligne"
	title "Installation service et Usager FTP"
	apt-get install -qy vsftpd
	useradd -d /home/ftpsyncuser -s /bin/bash ftpsyncuser 
	echo -n "Votre password FTP(ftpsyncuser2014) :"
	passwd ftpsyncuser 
	mkdir /home/ftpsyncuser
	chown -R ftpsyncuser:ftpsyncuser /home/ftpsyncuser

	sed -i "s/^#local_enable=YES$/local_enable=YES/g" /etc/vsftpd.conf
	sed -i "s/^#write_enable=YES$/write_enable=YES/g" /etc/vsftpd.conf
	service vsftpd restart
}


Set_Mail(){
	header "Installateur de boutique en ligne"
	title "Installation service MAIL Sortant"
	apt-get install -qy postfix mailutils
	#dpkg-reconfigure postfix
	echo "kdresdell@gmail.com" > /root/.forward

	postconf -e "relayhost = [smtp.gmail.com]:587"
	postconf -e "smtp_use_tls=yes"
	postconf -e "smtp_sasl_auth_enable = yes"
	postconf -e "smtp_sasl_password_maps = hash:/etc/postfix/sasl_passwd"
	postconf -e "smtp_sasl_security_options ="
	postconf -e "smtp_generic_maps = hash:/etc/postfix/generic"
	echo "[smtp.gmail.com]:587 kdresdell@gmail.com:mFrance&2012phileli" > /etc/postfix/sasl_passwd
	chown root:root /etc/postfix/sasl_passwd
	chmod 600 /etc/postfix/sasl_passwd
	postmap /etc/postfix/sasl_passwd
	echo "root@localhost ken@dresdell.com" >>/etc/postfix/generic 
	postmap /etc/postfix/generic
	service postfix restart
}


Set_Fail2ban(){
	header "Installateur de boutique en ligne"
	title "Installation service Fail2Ban"
	apt-get install -qy fail2ban
	title "Backup des configurations"
	mv /etc/fail2ban/jail.conf /etc/fail2ban/_jail.conf.origin 

	cp conf/fail2ban/wordpress-auth.conf /etc/fail2ban/filter.d/
	cp conf/fail2ban/jail.conf /etc/fail2ban/
	service fail2ban restart
}


Set_Firewall(){
	header "Installateur de boutique en ligne"
	title "Installation service de Firewall"
	cp -i bin/firewall.sh /etc/
	title "Activation du firewall au reboot"
	chmod +x /etc/firewall.sh
	sed -i '\/etc\/firewall.sh/d' /etc/rc.local
	sed -i -e '$i \/etc/firewall.sh' /etc/rc.local
}


Set_Swap(){
	header "Installateur de boutique en ligne"
	title "Installation Swap File 1024M"
	dd if=/dev/zero of=/swapfile bs=1M count=1024
	mkswap /swapfile
	swapon /swapfile
	echo "/swapfile       none    swap    sw      0       0" >> /etc/fstab 
	echo 10 | sudo tee /proc/sys/vm/swappiness
	echo vm.swappiness = 10 | sudo tee -a /etc/sysctl.conf
	sudo chown root:root /swapfile 
	sudo chmod 0600 /swapfile
	swapon -s
	free
}






source lib/lib.sh
##################################################################
#
#
#    PROGRAMME PRINCIPAL
#
#
##################################################################


VERSION="0.1"
ARG=$1


header "Installateur de boutique en ligne"


if [ "$ARG" == "all" ]; then
	UpdateOS
else 
	echo -n "Update and upgrade du OS (y/n) :"
	read a
	if [ "$a" == "y" ]; then
		UpdateOS
	fi
fi




if [ "$ARG" == "all" ]; then
	Set_Hostname
else 
	echo -n "Set_Hostname (y/n) :"
	read a
	if [ "$a" == "y" ]; then
		Set_Hostname
	fi
fi




if [ "$ARG" == "all" ]; then
	Set_SSH
else 
	echo -n "Set_SSH (y/n) :"
	read a
	if [ "$a" == "y" ]; then
		Set_SSH
	fi
fi


if [ "$ARG" == "all" ]; then
	Set_MySQL
else 
	echo -n "Set_MySQL (y/n) :"
	read a
	if [ "$a" == "y" ]; then
		Set_MySQL
	fi
fi


if [ "$ARG" == "all" ]; then
	Set_Nginx
else 
	echo -n "Set_Nginx (y/n) :"
	read a
	if [ "$a" == "y" ]; then
		Set_Nginx
	fi
fi


if [ "$ARG" == "all" ]; then
	Set_Wordpress
else 
	echo -n "Set_Wordpress (y/n) :"
	read a
	if [ "$a" == "y" ]; then
		Set_Wordpress
	fi
fi


if [ "$ARG" == "all" ]; then
	Set_Woo
else 
	echo -n "Set_Woo (y/n) :"
	read a
	if [ "$a" == "y" ]; then
		Set_Woo
	fi
fi



if [ "$ARG" == "all" ]; then
	Set_Themes
else 
	echo -n "Set_Themes (y/n) :"
	read a
	if [ "$a" == "y" ]; then
		Set_Themes
	fi
fi



if [ "$ARG" == "all" ]; then
	Set_Ftp
else 
	echo -n "Set_Ftp (y/n) :"
	read a
	if [ "$a" == "y" ]; then
		Set_Ftp
	fi
fi



if [ "$ARG" == "all" ]; then
	Set_Mail
else 
	echo -n "Set_Mail (y/n) :"
	read a
	if [ "$a" == "y" ]; then
		Set_Mail
	fi
fi


if [ "$ARG" == "all" ]; then
	Set_Fail2ban
else 
	echo -n "Set_Fail2ban (y/n) :"
	read a
	if [ "$a" == "y" ]; then
		Set_Fail2ban
	fi
fi



if [ "$ARG" == "all" ]; then
	Set_Firewall
else 
	echo -n "Set_Firewall (y/n) :"
	read a
	if [ "$a" == "y" ]; then
		Set_Firewall
	fi
fi


if [ "$ARG" == "all" ]; then
	Set_Swap
else 
	echo -n "Set_Swap (y/n) :"
	read a
	if [ "$a" == "y" ]; then
		Set_Swap
	fi
fi



echo "Vous devez rebooter pour activer la Swap"
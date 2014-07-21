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

# header "Installateur de boutique en ligne"
# title "Mise a jour du OS"
# apt-get update  -qy
# apt-get upgrade -qy

# header "Installateur de boutique en ligne"
# title "Setup SSH-Server on TCP 222"
# apt-get install openssh-server -qy
# sed -i "s/^Port 22$/Port 2222/g" /etc/ssh/sshd_config
# service ssh restart 

header "Installateur de boutique en ligne"
title "Installation du Firewall"
cp -i bin/firewall.sh /etc/
title "Activation du firewall au reboot"
chmod +x /etc/firewall.sh
sed -i '\/etc\/firewall.sh/d' /etc/rc.local
sed -i -e '$i \/etc/firewall.sh' /etc/rc.local


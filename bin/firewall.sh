#!/bin/bash

#     FIREWALL - IPTABLES
#
#     Ken Dresdell, kdresdell@gmail.com
#     2014-07-20


#     WAIT 69 SECONDS BEFORE APPLYING THE RULES. JUST 
#     TO GIVE TIME TO THE ADMIN TO ENTER AFTER A REBOOT

      sleep 1m

#  1. Definition des variales

      IPTABLES="/sbin/iptables"

#  2. Iptables Configuration.

      /sbin/depmod -a
      /sbin/modprobe ip_tables
      /sbin/modprobe ip_conntrack
      /sbin/modprobe ip_conntrack_ftp
      /sbin/modprobe iptable_filter
      /sbin/modprobe iptable_mangle
      /sbin/modprobe iptable_nat
      /sbin/modprobe ipt_LOG
      /sbin/modprobe ipt_limit
      /sbin/modprobe ipt_state

#  3. Flushing All Rules set up.

      $IPTABLES -F
      $IPTABLES -X
      $IPTABLES -Z
      $IPTABLES -t nat -F
      $IPTABLES -t mangle -F

#  4. Configuration des politiques par defaut

      $IPTABLES -P INPUT DROP
      $IPTABLES -P OUTPUT DROP
      $IPTABLES -P FORWARD DROP


#  5. Pour accélérer le traitement des paquets TCP, accepter tout ce 
#     qui est en relation avec une communication déjà établie et vérifiée.

      $IPTABLES -A INPUT -m state --state ESTABLISHED,RELATED -j ACCEPT
      $IPTABLES -A OUTPUT -m state --state ESTABLISHED,RELATED -j ACCEPT
      $IPTABLES -A FORWARD -m state --state ESTABLISHED,RELATED -j ACCEPT


#  6. Regles de filtrage des services locales

      $IPTABLES -A INPUT -i lo -j ACCEPT

      $IPTABLES -A INPUT -p tcp --dport 2222 -j ACCEPT
      $IPTABLES -A INPUT -p tcp --dport 80 -j ACCEPT
      $IPTABLES -A INPUT --source 192.168.1.68 -p tcp --dport 21 -j ACCEPT 

      $IPTABLES -A INPUT --source 207.134.128.166 -j ACCEPT
      $IPTABLES -A INPUT --source 207.134.128.6 -j ACCEPT
      $IPTABLES -A INPUT -j LOG --log-prefix "FW INPUT NoAuth!"


#  7. Filtres de sortie

      $IPTABLES -A OUTPUT -p icmp -j ACCEPT
      $IPTABLES -A OUTPUT -p udp -j ACCEPT
      $IPTABLES -A OUTPUT -p tcp -j ACCEPT

      $IPTABLES -A OUTPUT -j LOG --log-prefix "FW OUTPUT NoAuth!"

#!/bin/bash
#
#  Copyright (C) 2014
#  Ken Dresdell
#  All Rights Reserved
#
#
#  Last update 2014-07-20


source lib/lib.sh



function install_A {

  header "Installation PBX-IP KDC $VERSION" 

  title "Installation de bash"
  pkg_add -r bash 
  # >>$PBX_INSTALL_LOG 2>>$PBX_ERROR_LOG

  title "Installation de rsync"
  pkg_add -r rsyn 
  # >>$PBX_INSTALL_LOG 2>>$PBX_ERROR_LOG


  title "Install /etc/asterisk/manager.conf"
  rm -rf /etc/asterisk/manager.conf
  cp $CONF/manager.conf /etc/asterisk/manager.conf


}






##################################################################
#
#
#    PROGRAMME PRINCIPAL
#
#
##################################################################



  VERSION="1.0"
  
  ASTERISK="asterisk-1.4.26.1"
  GUI="asterisk-gui"
  SOURCES="/root/emPBXIP/SOURCES/"
  CONF="/root/emPBXIP/CONF"

  PBX_INSTALL_LOG="/root/PBX_INSTALL_LOG"
  PBX_ERROR_LOG="/root/PBX_ERROR_LOG"



  install_asterisk
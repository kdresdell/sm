#!/bin/bash

##
## ADD THE FOLOWING CONJOB
##

#*/30 * * * * /root/jmdstore/bin/check_new_files.sh > /dev/null 2>&1


if [ `find /home/kdresdell/test/ -name "*.c2sv"` ]

then
  echo "Nouveau fichier CSV JMD" | mail -s "Nouveau CSV JMD" kdresdell@gmail.com 
fi

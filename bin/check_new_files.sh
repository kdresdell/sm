#!/bin/bash

##
## ADD THE FOLOWING CONJOB
##

#*/30 * * * * /root/jmdstore/bin/check_new_files.sh > /dev/null 2>&1


if [ `find /home/ftpsyncuser/ -name "*.csv"` ]

then
  echo "Nouveau fichier CSV JMD" | mail -s "Nouveau CSV JMD" kdresdell@gmail.com
  #python /root/jmdstore/dev/csv.... 
fi

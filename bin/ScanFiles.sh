#!/bin/bash

##
## ADD THE FOLOWING CONJOB
##

#*/30 * * * * /root/jmdstore/bin/check_new_files.sh > /dev/null 2>&1


FILES=/home/ftpsyncuser/*.CSV
MAIL_USER="kdresdell@gmail.com"
DONE_PATH="/home/GoogleDrive/"


for f in $FILES
do
  if [ -f $f ]; then
    echo "Nouveau fichier CSV de JMD" | mail -s "Nouveau CSV JMD" $MAIL_USER
    /root/jmdstore/bin/CONV_CSV.py  $f
    mv $f $DONE_PATH
  fi

done

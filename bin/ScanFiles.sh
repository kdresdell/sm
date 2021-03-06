#!/bin/bash

##
## ADD THE FOLOWING CONJOB
##

#*/30 * * * * /root/jmdstore/bin/check_new_files.sh > /dev/null 2>&1


FILES=/home/ftpsyncuser/WEB.CSV
MAIL_USER="kdresdell@gmail.com"
DONE_PATH="/home/GoogleDrive"
U_WEB_FILE="/usr/share/nginx/html/CSV_QUEUE/U_WEB.csv"

TT=$(date -d "today" +"%Y%m%d%H%M")


for f in $FILES
do
  if [ -f $f ]; then

    # CONVERTING JMD'S CSV FILE FOR WPALLIMPORT SPEC
    /root/jmdstore/bin/CONV_CSV.py  $f

    # VALIDE SI LE FICHIER DE CONVERSION EST PRESENT
    if [ -f $U_WEB_FILE ]; then
      #echo "There is U_WEB_FILE..................................."
      #echo "Starting the import trigger..........................."
      ## OLD : curl --insecure "https://www.sportsjmd.com/wp-cron.php?import_key=LhZ0aEx5tfq&import_id=18&action=trigger" &
      curl --insecure "https://www.sportsjmd.com/wp-cron.php?import_key=LhZ0aEx5tfq&import_id=19&action=trigger" &
      triggerPID=$!
      #echo "triggerPID is $triggerPID"
      echo "Nouveau fichier CSV de JMD. PID is $triggerPID" | mail -s "Nouveau $triggerPID CSV JMD" $MAIL_USER

      while test -d /proc/$triggerPID; do
        #echo "Curl processing aux 2 minutes durant l'importation......"
        ## OLD :curl --insecure "https://www.sportsjmd.com/wp-cron.php?import_key=LhZ0aEx5tfq&import_id=18&action=processing"
        curl --insecure "https://www.sportsjmd.com/wp-cron.php?import_key=LhZ0aEx5tfq&import_id=19&action=processing"
        sleep 120
      done

      # JE RENOMME LES FICHIERS AVEC TIME STAMP POUR GARDER UN HISTORIQUE
      mv $f $DONE_PATH/WEB_$TT.CSV
      mv $U_WEB_FILE $DONE_PATH/U_WEB_$TT.csv

    fi
  fi

done


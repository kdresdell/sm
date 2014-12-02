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

    # VALIDE SI LE FICHIER DE CONVERSION EST PRESENT
    if [ -f $U_WEB_FILE ]; then
      #echo "There is U_WEB_FILE..................................."
      #echo "Starting the import trigger..........................."
      curl --insecure "https://www.sportsjmd.com/wp-cron.php?import_key=LhZ0aEx5tfq&import_id=18&action=trigger" &
      triggerPID=$!
      #echo "triggerPID is $triggerPID"
      echo "Nouveau fichier CSV de JMD. PID is $triggerPID" | mail -s "Nouveau $triggerPID CSV JMD" $MAIL_USER

      while test -d /proc/$triggerPID; do
        echo "Curl processing aux 2 minutes durant l'importation......"
        curl --insecure "https://www.sportsjmd.com/wp-cron.php?import_key=LhZ0aEx5tfq&import_id=18&action=processing"
        sleep 60
      done

      # JE RENOMME LES FICHIERS AVEC TIME STAMP POUR GARDER UN HISTORIQUE
      mv $f $DONE_PATH/WEB_$TT.CSV
      mv $U_WEB_FILE $DONE_PATH/U_WEB_$TT.csv

    fi


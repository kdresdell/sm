#!/bin/bash

##
## ADD THE FOLOWING CONJOB
##

#*/30 * * * * /root/jmdstore/bin/check_new_files.sh > /dev/null 2>&1


FILES=/home/ftpsyncuser/WEB.CSV
MAIL_USER="kdresdell@gmail.com"
DONE_PATH="/home/GoogleDrive/"
U_WEB_FILE="/usr/share/nginx/html/CSV_QUEUE/U_WEB.csv"


for f in $FILES
do
  if [ -f $f ]; then
    echo "Nouveau fichier CSV de JMD" | mail -s "Nouveau CSV JMD" $MAIL_USER
    echo "There is a WEB.CSV file in FTPSYNCUSER FOLDER............"
    echo "CONVERTING RICHARD'S CSV FILE FOR WPALLIMPORT SPEC......"
    #/root/jmdstore/bin/CONV_CSV.py  $f

    # VALIDE SI LE FICHIER DE CONVERSION EST PRESENT
    if [ -f $U_WEB_FILE ]; then
      echo "There is U_WEB_FILE..................................."
      echo "Starting the import trigger..........................."
      curl --insecure "https://www.sportsjmd.com/wp-cron.php?import_key=LhZ0aEx5tfq&import_id=18&action=trigger"
      echo "Starting the processing (2 minutes freg) trigger......"
      curl --insecure "https://www.sportsjmd.com/wp-cron.php?import_key=LhZ0aEx5tfq&import_id=18&action=processing"
      sleep 120
      curl --insecure "https://www.sportsjmd.com/wp-cron.php?import_key=LhZ0aEx5tfq&import_id=18&action=processing"
      mv $f $DONE_PATH
      mv $U_WEB_FILE $DONE_PATH
    fi
  fi

done

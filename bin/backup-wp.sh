#!/bin/bash
 
# Backup MySQL database and compress with max niceness

mysqldump -u wp_admin -p wordpress | nice -n 19 gzip -f > wp-db-backup.sql.gz

DBNAME="wordpress"
DBPASSWORD="wpadmin&2014"
DBUSER="wp_admin"


DATE=`date +"%Y%m%d"`
SQLFILE=$DBNAME-${DATE}.sql

mysqldump -u $DBUSER -p\'wpadmin&2014' $DBNAME > $SQLFILE
#gzip $SQLFILE



#tar czvf wp-backup.tgz /usr/share/nginx/html/

#echo "File backup done"


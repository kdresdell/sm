#!/bin/bash
 
DBNAME="wordpress"
DBPASSWORD="wpadmin&2014"
DBUSER="wp_admin"
BK_PATH="/root/backups/"

DATE=`date +"%Y%m%d"`
SQLFILE=$BK_PATH$DBNAME-${DATE}.sql
WEBFILE=$BK_PATH'WEBFILES'-${DATE}.tar.gz


##
## ADD THE FOLOWING CONJOB
##

# 0 2 * * * /root/jmdstore/bin/backup-wp.sh > /dev/null 2>&1





##
## BACKUP THE DATABASE
##

mysqldump -u $DBUSER -p'wpadmin&2014' $DBNAME > $SQLFILE
gzip $SQLFILE


##
## BACKUP WEB FILES
##

#tar czvf wp-backup.tgz /usr/share/nginx/html/
tar cvf - /usr/share/nginx/html/ --exclude='*TMP_IMG*' | gzip -9 - > $WEBFILE



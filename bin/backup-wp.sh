#!/bin/bash
 
# Backup MySQL database and compress with max niceness

mysqldump -u wp_admin -p wordpress | nice -n 19 gzip -f > wp-db-backup.sql.gz


echo "Database backup done"
 
# Backup Files
# Change directory to where wordpress is contained


tar czvf wp-backup.tgz /usr/share/nginx/html/

echo "File backup done"


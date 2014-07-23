CREATE DATABASE wordpress;
CREATE USER 'wp_admin'@'localhost' IDENTIFIED BY 'wpadmin&2014';
CREATE USER 'kdresdell'@'%' IDENTIFIED BY 'monsterinc00';
GRANT ALL PRIVILEGES ON wordpress.* TO 'wp_admin'@'localhost';
GRANT ALL PRIVILEGES ON wordpress.* TO 'kdresdell'@'%';
FLUSH PRIVILEGES;

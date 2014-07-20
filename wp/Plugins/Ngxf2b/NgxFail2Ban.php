<?php
/*
Plugin Name: NgxFail2Ban
Description: Write custom NGINX logs if user try to login without success. Necessary for fail2ban.
Version: The Plugin's Version Number, e.g.: 1.0
Author: Ken Dresdell
*/



function my_login_failed_403() {
    status_header( 403 );
}
add_action( 'wp_login_failed', 'my_login_failed_403' );

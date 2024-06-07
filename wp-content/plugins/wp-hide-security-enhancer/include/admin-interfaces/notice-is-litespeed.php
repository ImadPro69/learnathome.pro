<?php
        if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

    <li>
        <p>
            <span class="dashicons dashicons-flag error critical"></span> <?php _e( "Your site runs on LiteSpeed ! Before starting, ensure your server is properly configured and it processes the .htaccess file, or there might be layout and functionality breaks.", 'wp-hide-security-enhancer' ) ?> <?php _e( "For more details check at", 'wp-hide-security-enhancer' ) ?> <a target="_blank" href="https://wp-hide.com/setup-wp-hide-on-litespeed/">Setup WP Hide on LiteSpeed</a>
            <br /><?php _e( "Also, once the plugin options changed, a LiteSpeed service may be required. Through SSH run the command", 'wp-hide-security-enhancer' ) ?> <b class="highlight">sudo systemctl restart lsws</b>
            <br /><?php _e( "On certain servers (e.g. Hostinger ) the service restart may not be required.", 'wp-hide-security-enhancer' ) ?>
        </p>
    </li>
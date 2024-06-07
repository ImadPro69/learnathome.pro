<?php
        if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

        <li>
            <p><span class="dashicons dashicons-flag error"></span><b><?php _e("Rewrite test for static php files failed! ", 'wp-hide-security-enhancer') ?></b> 
            <?php echo $result ?>
            <br /><?php _e("This is a <b>soft error</b> and impact only the option at Rewrite > Theme > 'Remove description header from Style file' and should be disabled until the issue fixed.", 'wp-hide-security-enhancer') ?>
            </p>
        </li>
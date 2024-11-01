<?php
/**
 * WP Users Media Uninstall - Removes all WP Users Media options from DB when user deletes the plugin via WordPress backend.
 * 
 * @since 4.2.0
 */
if(!defined('WP_UNINSTALL_PLUGIN')){
    exit();
}
delete_option('wpusme_settings');
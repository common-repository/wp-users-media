<?php
/**
 * Plugin Name: WP Users Media
 * Plugin URI: https://wordpress.org/plugins/wp-users-media/
 * Description: WP Users Media is a WordPress plugin that displays only the current users media files and attachments in WP Admin.
 * Version: 4.2.3
 * Author: Damir Calusic
 * Author URI: https://www.damircalusic.com/
 * Text Domain: wpusme
 * Domain Path: /languages/
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if(!defined('ABSPATH')) exit;

// Define global variables
define('WPUSME_VERSION', '4.2.3');

/**
* Check if the Class exists otherwise create it
*/
if(!class_exists('WPUM_Functions')){
    /**
     * Class WPUM_Functions
     */
    class WPUM_Functions {
        /**
         * The reference the WPUM_Functions instance of this class.
         *
         * @since 4.2.0
         * @var $instance
         */
        protected static $instance;

        /**
         * The refrence to the settings page url
         * 
         * @since 4.2.0
         * @string settings_page_endpoint
         */
        public $settings_page_endpoint = 'wpusme_settings_page';

        /**
         * Donate links
         * 
         * @since 4.2.1
         * @string $donate_link_paypal
         * @string $donate_link_patreon
         */
        public $donate_link_paypal = 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=AJABLMWDF4RR8&source=url';
        public $donate_link_patreon = 'https://www.patreon.com/webkreativ';
       
        /**
         * Contains all the settings keys for the the general settings
         *
         * @since 4.2.0
         * @array $wpusme_general
         */
        public $wpusme_general = [];

        /**
         * Contains all the settings keys for the WP User Roles
         *
         * @since 4.2.0
         * @array $wpusme_roles
         */
        public $wpusme_roles = [];
        
        /**
         * Contains all unserialized settings
         * 
         * @since 4.2.0
         * @array $wpusme_settings
         */
        public $wpusme_settings = [];

        /**
         * Contains the proper format of settings
         * 
         * @since 4.2.0
         * @array $wpusme_keys
         */
        public $wpusme_keys = [];

        /**
         * Protected constructor to prevent creating a new instance of the WPUSME_Functions via the `new` operator from outside of this class.
         */
        protected function __construct() {
            add_action('plugins_loaded', array($this, 'wpusme_plugins_loaded'));
            $this->init_wpusme_functions();
        }

        /**
         * Returns the WPUSME_Functions instance of this class.
         *
         * @since 4.2.0
         * @return self::$instance The WPUSME_Functions instance.
         */
        public static function get_instance(){
            if(null === self::$instance){
                self::$instance = new self();
            }

            return self::$instance;
		}
		
		/** 
         * Alter information directly on plugins loaded
         *
         *  @since 4.2.0 
         */
        public function wpusme_plugins_loaded(){
            add_filter('plugin_action_links_'.plugin_basename(__FILE__), array($this, 'wpusme_plugin_action_links'));
		}
		
		/**
		 * Adds plugin action links
         * 
         * @since 4.2.0
		 */
		public function wpusme_plugin_action_links($links){
			$plugin_links = array(
                '<a href="/wp-admin/options-general.php?page='.$this->settings_page_endpoint.'">'.__('Settings', 'wpusme').'</a>',
                '<a href="'.$this->donate_link_paypal.'" target="_blank">'.__('Donate (Paypal)','wpusme').'</a>',
                '<a href="'.$this->donate_link_patreon.'" target="_blank">'.__('Donate (Patreon)','wpusme').'</a>',
			);

			return array_merge($plugin_links, $links);
		}

        /**
         * Initiate the WPUSME Plugin
         * 
         * @since 4.2.0
         */
        public function init_wpusme_functions(){
            add_action('admin_enqueue_scripts', array($this, 'wpusme_admin_enqueue_scripts'));
			add_action('admin_menu', array($this, 'wpusme_admin_menu'));
			add_action('init', array($this, 'wpusme_init'));
			add_action('pre_get_posts', array($this, 'wpusme_filter_media_files'));
			add_action('wp_ajax_wpusme_save_settings', array($this, 'wpusme_save_settings'));
		}

		/**
         * Alter the wp admin menues in sidebar 
         * 
         * @since 4.2.0
         */
        public function wpusme_admin_enqueue_scripts(){
            wp_enqueue_style('wp-users-media-style', plugin_dir_url(__FILE__).'assets/css/wpusme-styles.css', false, WPUSME_VERSION, false);
            wp_enqueue_script('wp-users-media-script', plugin_dir_url(__FILE__).'assets/js/wpusme-scripts.js', false, WPUSME_VERSION, true);   
		}
		
		/**
         * Alter the wp admin menues in sidebar 
         * 
         * @since 4.2.0
         */
        public function wpusme_admin_menu(){
            add_submenu_page('options-general.php', __('WP Users Media','wpusme'), __('WP Users Media','wpusme'), 'manage_options', $this->settings_page_endpoint, array($this, $this->settings_page_endpoint));
            
            if($this->wpusme_valid_key('wpusme_enable_side_menu_link')){ 
                add_menu_page(__('WP Users Media','wpusme'), __('WP Users Media','wpusme'), 'manage_options', __FILE__, $this->settings_page_endpoint, 'dashicons-media-archive', 10); 
            }
        }
		
		/**
         * Alter functions and data on init
         * 
         * @since 4.2.0
         */
        public function wpusme_init(){
            global $pagenow;

            // Run the needed settings initiatings
            $this->init_wpusme_settings();
        }

        /**
         * Initiate the WPUSME Plugin Settings
         * 
         * @since 4.2.0
         */
        public function init_wpusme_settings(){
			global $wp_roles;
			$roles = $this->wpusme_check_if_object_isset($wp_roles->get_names());

            // Load languages
            load_plugin_textdomain('wpusme', false, basename(dirname(__FILE__)).'/languages');

            $this->wpusme_general = array(
                'wpusme_enable_admin_only'		=> __('Enable so Admins can only view their own attachments.','wpusme'),
                'wpusme_enable_side_menu_link'	=> __('Enable shortcut for WP Users Media in sidebar menu','wpusme'),
            );
	
			if(is_array($roles)){
				foreach($roles as $role => $name){
					if($role !== 'administrator'){
						$this->wpusme_roles['wpusme_enable_'.$role.'_only'] = sprintf(__('Enable so %s can only view their own attachments.','wpusme'), $name);
					}
				}
			}

            // Get settings from the database
            $this->wpusme_settings = unserialize(get_option('wpusme_settings'));
            
            // Set correct output of the settings
            $this->wpusme_keys = is_array($this->wpusme_settings) ? $this->wpusme_settings : [];
        }

        /**
         * Check that the key exists and returns valid result 
         * 
         * @since 4.2.0
         */
        public function wpusme_valid_key($key){
            return ($this->wpusme_keys && (array_key_exists($key, $this->wpusme_keys) && $this->wpusme_keys[$key] == 'true')) ? true : false;
        }

        /**
         * Create and display the Settings Page in admin
         * 
         * @since 4.2.0
         */
        public function wpusme_settings_page(){ ?>
            <form id="wpusme_elements_form">
                <div class="wpusme-header">
                    <h1><?php _e('WP Users Media','wpusme'); ?></h1>
                    <nav class="wpusme-tabs-wrapper">
                        <a href="#" class="wpusme-tab active" data-id="wpusme_general"><?php echo __('General','wpusme').' <small>('.count($this->wpusme_general).')</small>'; ?></a>
                        <a href="#" class="wpusme-tab" data-id="wpusme_roles"><?php echo __('User roles','wpusme').' <small>('.count($this->wpusme_roles).')</small>'; ?></a>
                    </nav>
                </div>
                <div class="wpusme-check-body">
                    <div id="wpusme_general" class="div-tab">
                        <div class="notice notice-info inline"><p><?php _e('General basic options.','wpusme'); ?></p></div>
                        <?php foreach($this->wpusme_general as $key => $desc){ ?>
                            <h3>
                                <span><?php echo $desc; ?></span>
                                <label class="switch">
                                    <input id="<?php echo $key; ?>" type="checkbox" name="<?php echo $key; ?>" value="1" <?php echo (($this->wpusme_valid_key($key)) ? 'checked="checked"' : ''); ?>>
                                    <span for="<?php echo $key; ?>" class="slider"></span>
                                </label>
                            </h3>
                        <?php } ?>
                    </div>
                    <div id="wpusme_roles" class="div-tab">
                        <div class="notice notice-info inline"><p><?php _e('Leave UNCHECKED and all users will be able to only view their own attachments. If you CHECK a user role, those users will view only their own attachments, other user roles will be able to view all attachments from all users.','wpusme'); ?></p></div>
                        <?php foreach($this->wpusme_roles as $key => $desc){ ?>
                            <h3>
                                <span><?php echo $desc; ?></span>
                                <label class="switch">
                                    <input id="<?php echo $key; ?>" type="checkbox" name="<?php echo $key; ?>" value="1" <?php echo (($this->wpusme_valid_key($key)) ? 'checked="checked"' : ''); ?>>
                                    <span for="<?php echo $key; ?>" class="slider"></span>
                                </label>
                            </h3>
                        <?php } ?>
                    </div>
                    <div id="wpusme_popup">
                        <div id="wpusme_popup_container">
                            <span id="wpusme_icon" class="dashicons dashicons-yes"></span>
                            <h2 id="wpusme_title"><?php _e('Saved','wpusme'); ?></h2>
                            <p id="wpusme_content"><?php _e('All your settings have been saved.','wpusme'); ?></p>
                            <button id="wpusme_button" type="button"><?php _e('Ok','wpusme'); ?></button>
                        </div>
                    </div>
                    <p class="wpusme-footer">
                        <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','wpusme'); ?>">
                        <a href="https://twitter.com/damircalusic/" target="_blank"><?php _e('Follow on Twitter','wpusme'); ?></a> | 
                        <a href="<?php echo $this->donate_link_paypal; ?>" target="_blank"><?php _e('Donate (Paypal)','wpusme'); ?></a> | 
                        <a href="<?php echo $this->donate_link_patreon; ?>" target="_blank"><?php _e('Donate (Patreon)','wpusme'); ?></a>
                        <span id="info" class="dashicons dashicons-info"></span>
                        <span id="version"><?php echo WPUSME_VERSION; ?></span>
                    </p>
					<div id="wpusme-info-notice" class="notice notice-info inline"><p><?php _e('What the plugin does is to disable the ability for users to access other members files and attachments through the Media Button and Featured Image sections. This is really good because maybe you have Authors, Contributors and Subscribers that write posts etc. and you do not want them to be able to use other members media files to their own content.','wpusme'); ?></p></div>
                </div>
            </form>
        <?php 
		}
		
		/**
		 * Filter attachments for the specific user
		 * 
		 * @since 4.2.0 
		 */
		public function wpusme_filter_media_files($wp_query){
			// Make sure the user is logged in first
			if(is_user_logged_in()){
				global $current_user;

				// Check so the $wp_query->query['post_type'] isset and that we are on the attachment page in admin
				if(isset($wp_query->query['post_type']) && (is_admin() && $wp_query->query['post_type'] === 'attachment')){
					
					// Display the admins attachments only for the admin self.
					if($this->wpusme_valid_key('wpusme_enable_admin_only')){
						if(current_user_can('manage_options')){
							$wp_query->set('author', $current_user->ID);
						}
					}
					
					// Check if we have checked a role and display attachments only for the users in the role only
					if(!current_user_can('manage_options')){
						$count_roles = 0;

						if(is_array($this->wpusme_roles)){
							foreach($this->wpusme_roles as $role => $name){
								if($this->wpusme_valid_key($role)){
									$role = str_replace('wpusme_enable_', '', str_replace('_only', '', $role));
									
									if(in_array($role, $current_user->roles)){
										$wp_query->set('author', $current_user->ID);
									}

									$count_roles++;
								}
							}
						}
					
						// Default setting; All users can view only their own attachments except Admin
						if($count_roles == 0){
							$wp_query->set('author', $current_user->ID);
						}
					}
				}
			}
		}

		/** 
		 * Recount attachments for the specific user 
		 * 	
		 * @since 2.0.0
		 */
		/*public function wpusme_recount_attachments($counts_in){
			global $wpdb;
			global $current_user;

			$and = wp_post_mime_type_where(''); // Default mime type // AND post_author = {$current_user->ID}
			$count = $wpdb->get_results("SELECT post_mime_type, COUNT(*) AS num_posts FROM $wpdb->posts WHERE post_type = 'attachment' AND post_status != 'trash' AND post_author = {$current_user->ID} $and GROUP BY post_mime_type", ARRAY_A);

			$counts = array();
			
			foreach((array)$count as $row){
				$counts[$row['post_mime_type']] = $row['num_posts'];
			}

			$counts['trash'] = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'attachment' AND post_author = {$current_user->ID} AND post_status = 'trash' $and");

			return $counts;
		}*/


        /** 
         * Save settings for the plugin
         * 
         * @since 4.2.0
         */
        public function wpusme_save_settings(){
            $data = ($_POST['data']) ? $_POST['data'] : '';
            
            if($data){
                $cleanedSettings = $this->wpusme_clean_settings($data);
                $settings = serialize($cleanedSettings);
                update_option('wpusme_settings', $settings);
            }
			
			// Always DIE when dealing with Ajax
            die();
        }

        /** 
         * Clean settings from unwanted special characters
         * 
         * @since 4.2.0
         */
        public function wpusme_clean_settings($data){
            if(!is_array($data)) {
                return stripslashes($data);
            }
        
            $newSettings = [];
            
            foreach($data as $key => $value){
                $newSettings[$key] = $this->wpusme_clean_settings($value);
            }

            return $newSettings;
		}
		
		/**	
		 * Checks if the object isset
		 * 	
		 * @since 4.2.0 
		 */
		public function wpusme_check_if_object_isset($object){
			return ($object !== null) ? $object : '';
		}

    } // End CLASS WPUSME_Functions
} // End Class Check if exists

// In BaseClass constructor
WPUM_Functions::get_instance();
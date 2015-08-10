<?php
/**
 * Plugin Name: BadgeOS Community Activity Add-On
 * Plugin URI: http://www.d2-si.fr/
 * Description: This BadgeOS add-on enable "Community Activity" features
 * Author: D2SI
 * Version: 1.0.0
 * Author URI: https://www.d2-si.fr/
 * License: GNU AGPLv3
 * License URI: http://www.gnu.org/licenses/agpl-3.0.html
 */

/**
 * Our main plugin instantiation class
 *
 * This contains important things that our relevant to
 * our add-on running correctly. Things like registering
 * custom post types, taxonomies, posts-to-posts
 * relationships, and the like.
 *
 * @since 1.0.0
 */
 class BadgeOS_Community_Activity {

	/**
	 * Get everything running.
	 *
	 * @since 1.0.0
	 */
	function __construct() {

		// Define plugin constants
		$this->basename       = plugin_basename( __FILE__ );
		$this->directory_path = plugin_dir_path( __FILE__ );
		$this->directory_url  = plugins_url( dirname( $this->basename ) );
		
		// Load translations : no need for now
		// load_plugin_textdomain( 'badgeos-community-activity', false, dirname( $this->basename ) . '/languages' );

		// Run our activation and deactivation hooks
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		// If BadgeOS is unavailable, deactivate our plugin
		add_action( 'admin_notices', array( $this, 'maybe_disable_plugin' ) );

		// Include our other plugin files
		add_action( 'plugins_loaded', array( $this, 'includes' ) );
		add_action( 'init', array( $this, 'register_scripts_and_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'badgeos_community_activity_addon_script'));

	} /* __construct() */


	/**
	 * Include our plugin dependencies
	 *
	 * @since 1.0.0
	 */
	public function includes() {

		// If BadgeOS is available...
        if ( $this->meets_requirements() ) {
            require_once( $this->directory_path . '/includes/community_activity.php' );
            require_once( $this->directory_path . '/includes/shortcodes/badgeos_last_earners.php' );
		}

	} /* includes() */

	/**
	 * Register all Community Activity scripts and styles
	 *
	 * @since  1.3.0
	 */
	function register_scripts_and_styles() {
		// Register scripts
        wp_register_script( 'badgeos-community-activity', $this->directory_url . '/js/badgeos-community-activity.js', array( 'jquery' ), '1.1.0', true );
		wp_register_style( 'badgeos-community-activity-front', $this->directory_url . '/css/badgeos-community-activity-front.css', null, '1.0.1' );
    }

    /**
     * Add filters 
     *
     * @since 1.0.0
     * @return null
     */
    function badgeos_community_activity_addon_script() {
    	wp_enqueue_script( 'badgeos-community-activity' );
	    wp_enqueue_style( 'badgeos-community-activity-front' );
	    
        global $user_ID;

    	$data = array(
    		'ajax_url'    => esc_url( admin_url( 'admin-ajax.php', 'relative' ) ),
    		'user_id'     => $user_ID,
    	);
    	wp_localize_script( 'badgeos-community-activity', 'badgeos_community_activity', $data );
    }
	

	/**
	 * Activation hook for the plugin.
	 *
	 * @since 1.0.0
	 */
	public function activate() {
		// If BadgeOS is available, run our activation functions
		if ( $this->meets_requirements() ) {
			// Do some activation things
		}

	} /* activate() */

	/**
	 * Deactivation hook for the plugin.
	 *
	 * Note: this plugin may auto-deactivate due
	 * to $this->maybe_disable_plugin()
	 *
	 * @since 1.0.0
	 */
	public function deactivate() {

		// Do some deactivation things.

	} /* deactivate() */

	/**
	 * Check if BadgeOS is available
	 *
	 * @since  1.0.0
	 * @return bool True if BadgeOS is available, false otherwise
	 */
	public static function meets_requirements() {

		if ( class_exists('BadgeOS') )
			return true;
		else
			return false;

	} /* meets_requirements() */

	/**
	 * Potentially output a custom error message and deactivate
	 * this plugin, if we don't meet requriements.
	 *
	 * This fires on admin_notices.
	 *
	 * @since 1.0.0
	 */
	public function maybe_disable_plugin() {

		if ( ! $this->meets_requirements() ) {
			// Display our error
			echo '<div id="message" class="error">';
			echo '<p>' . sprintf( __( 'BadgeOS Add-On requires BadgeOS and has been <a href="%s">deactivated</a>. Please install and activate BadgeOS and then reactivate this plugin.', 'badgeos-addon' ), admin_url( 'plugins.php' ) ) . '</p>';
			echo '</div>';

			// Deactivate our plugin
			deactivate_plugins( $this->basename );
		}

	} /* maybe_disable_plugin() */

} /* BadgeOS_Addon */

// Instantiate our class to a global variable that we can access elsewhere
$GLOBALS['badgeos_community_activity'] = new BadgeOS_Community_Activity();

function badgeos_community_activity_get_directory_path() {
	return $GLOBALS['badgeos_community_activity']->directory_path;
}
function badgeos_community_activity_get_directory_url() {
	return $GLOBALS['badgeos_community_activity']->directory_url;
}


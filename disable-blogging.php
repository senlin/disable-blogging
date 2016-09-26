<?php
/**
 * Plugin Name: Disable Blogging
 * Plugin URI: https://wordpress.org/plugins/disable-blog/
 * Description: Turn WordPress into a non-blogging, CMS platform by disabling posts, comments, feeds, and other related the blogging features.
 * Author: <a href="https://www.factmaven.com/#plugins">Fact Maven</a>
 * License: GPLv3
 * Text Domain: disable-blogging
 * Version: 2.0.0
 */

# If accessed directly, exit
if ( ! defined( 'ABSPATH' ) ) exit;

class Fact_Maven_Disable_Blogging_Plugin_Meta {

    function __construct() {
        # Add meta links to plugin page
        add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
        # Add link to plugin settings
        add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );

        # Settings API
        require_once dirname( __FILE__ ) . '/includes/settings-api.php';
        # Settings Page
        require_once dirname( __FILE__ ) . '/includes/settings-page.php';
        # General Settings
        require_once dirname( __FILE__ ) . '/includes/functions-general.php';
        # Profile Settings
        require_once dirname( __FILE__ ) . '/includes/functions-profile.php';

        # Instantiate the class
        new Fact_Maven_Disable_Blogging();
    }

    public function plugin_row_meta( $links, $file ) {
        # Display meta links on left side of the plugin
        if ( strpos( $file, plugin_basename( __FILE__ ) ) !== false ) {
            $meta = array(
                'support' => '<a href="https://wordpress.org/support/plugin/disable-blogging" target="_blank"><span class="dashicons dashicons-sos"></span> ' . __( 'Support' ) . '</a>',
                'review' => '<a href="https://wordpress.org/support/view/plugin-reviews/disable-blogging" target="_blank"><span class="dashicons dashicons-nametag"></span> ' . __( 'Review' ) . '</a>',
                'github' => '<a href="https://github.com/factmaven/disable-blogging" target="_blank"><span class="dashicons dashicons-randomize"></span> ' . __( 'GitHub' ) . '</a>'
            );
            $links = array_merge( $links, $meta );
        }
        return $links;
    }

    public function plugin_action_links( $links, $file ) {
        # IDisplay settings link on the right side of the plugin
        if ( $file == plugin_basename( __FILE__ ) && current_user_can( 'manage_options' ) ) {
            array_unshift(
                $links,
                sprintf( '<a href="options-general.php?page=blogging">%s</a>', __( 'Settings' ) )
            );
        }
        return $links;
    }
}

# Instantiate the class
new Fact_Maven_Disable_Blogging_Plugin_Meta();
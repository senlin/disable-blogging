<?php
/**
 * WordPress settings API demo class
 *
 * @author Fact Maven Corp.
 */

if ( ! class_exists( 'Fact_Maven_Disable_Blogging' ) ):
class Fact_Maven_Disable_Blogging {

    private $settings_api;

    function __construct() {
        $this->settings_api = new Fact_Maven_Disable_Blogging_Settings;

        add_action( 'admin_init', array($this, 'admin_init' ) );
        add_action( 'admin_menu', array($this, 'admin_menu' ) );
    }

    function admin_init() {
        // Set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        // Initialize settings
        $this->settings_api->admin_init();
    }

    function admin_menu() {
        add_options_page(
            'Blogging Settings', // Page title
            'Blogging', // Menu title
            'manage_options', // Capability
            'blogging', // URL slug
            array($this, 'plugin_page' ) // Callback function
            );
    }

    function get_settings_sections() {
        $sections = array(
            array(
                'id' => 'dsbl_basics',
                'title' => __( 'General Settings', 'dsbl' )
            ),
            array(
                'id' => 'dsbl_profile',
                'title' => __( 'Profile Page', 'dsbl' )
            ),
            array(
                'id' => 'dsbl_menu',
                'title' => __( 'Menu Settings', 'dsbl' )
            )
        );
        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        
        $options_contact = [];
        $options_contact['url'] = 'Website';
        foreach ( wp_get_user_contact_methods() as $value => $label ) {
            $options_contact[$value] = $label;
        }

        global $menu, $submenu;
        $options_menu = [];
        foreach ( $menu as $group => $item ) {
            if ( !empty( $item[0] ) ) {
                $options_menu[$item[2]] = $item[0];
            }
            else {
                $item[0] = '<span class="description">- Separator -</span>';
                $options_menu[$item[2]] = $item[0];
            }
        }
        $options_submenu = [];
        foreach ( $submenu as $group => $item ) {
            foreach ( $item as $key ) {
                $options_submenu[$key[2]] = $key[0];
            }
        }

        $settings_fields = array(
            'dsbl_basics' => array( // General Settings
                array(
                    'name' => 'text_val',
                    'label' => __( 'Text Input', 'dsbl' ),
                    'desc' => __( 'Text input description', 'dsbl' ),
                    'type' => 'text',
                    'default' => 'Title',
                    'sanitize_callback' => 'intval'
                )
            ),
            'dsbl_profile' => array( // Profile Settings
                array(
                    'name' => 'personal_options',
                    'label' => __( 'Personal Options', 'dsbl' ),
                    'type' => 'multicheck',
                    'options' => array(
                        'rich_editing' => 'Visual Editor',
                        'admin_color' => 'Admin Color Scheme',
                        'comment_shortcuts' => 'Keyboard Shortcuts',
                        'admin_bar_front' => 'Toolbar'
                    )
                ),
                array(
                    'name' => 'name',
                    'label' => __( 'Name', 'dsbl' ),
                    'type' => 'multicheck',
                    'options' => array(
                        'first_name' => 'First Name',
                        'last_name' => 'Last Name',
                        'nickname' => 'Nickname',
                        'display_name' => 'Display Name'
                    )
                ),
                array(
                    'name' => 'contact_info',
                    'label' => __( 'Contact Info', 'dsbl' ),
                    'type' => 'multicheck',
                    'options' => $options_contact
                ),
                array(
                    'name' => 'about_yourself',
                    'label' => __( 'About Yourself', 'dsbl' ),
                    'desc' => __( 'Avatar settings can be managed in <a href="' . get_site_url() . '/wp-admin/options-discussion.php#show_avatars">Discussion</a> page.', 'dsbl' ),
                    'type' => 'multicheck',
                    'options' => array(
                        'description' => 'Biographical Info',
                        'show_avatars' => 'Avatar Display'
                    )
                )
            ),
            'dsbl_menu' => array( // Menu Settings
                array(
                    'name' => 'redirect_menu',
                    'label' => __( 'Redirect hidden menu items to', 'dsbl' ),
                    'desc' => __( 'If none is selected, a denied message will be displayed instead.', 'dsbl' ),
                    'type' => 'select',
                    'default' => 'none',
                    'options' => array(
                        'index.php' => 'Dashboard',
                        'edit.php?post_type=page' => 'Pages',
                        'none' => '- None -'
                    )
                ),
                array(
                    'name' => 'hide_dashicons',
                    'label' => __( 'Hide all menu <a target="_blank" href="https://developer.wordpress.org/resource/dashicons">dashicons</a>', 'dsbl' ),
                    'desc' => __( 'The icons will only be shown when the menu is collapsed.', 'dsbl' ),
                    'type' => 'select',
                    'default' => 'no',
                    'options' => array(
                        'yes' => 'Yes',
                        'no' => 'No'
                    )
                ),
                array(
                    'name' => 'main_menu',
                    'label' => __( 'Main Menu', 'dsbl' ),
                    'desc' => __( 'Hiding each <strong>seperator</strong> will remove the spacing in between the menu items.', 'dsbl' ),
                    'type' => 'multicheck',
                    'default' => array(
                        'edit.php' => 'edit.php', // Posts
                        'edit-comments.php' => 'edit-comments.php', // Comments
                        'separator1' => 'separator1', // Separator
                        'separator2' => 'separator2' // Separator
                    ),
                    'options' => $options_menu
                ),
                array(
                    'name' => 'submenu',
                    'label' => __( 'Submenu', 'dsbl' ),
                    'type' => 'multicheck',
                    'default' => array(
                        'edit.php' => 'edit.php', // Posts > All Posts
                        'post-new.php' => 'post-new.php', // Posts > Add New
                        'edit-tags.php?taxonomy=category' => 'edit-tags.php?taxonomy=category', // Posts > Categories
                        'edit-tags.php?taxonomy=post_tag' => 'edit-tags.php?taxonomy=post_tag', // Posts > Tags
                        'tools.php' => 'tools.php', // Tools > Available Tools
                        'import.php' => 'import.php', // Tools > Import
                        'export.php' => 'export.php', // Tools > Export
                        'options-discussion.php' => 'options-discussion.php', // Settings > Discussion                   
                    ),
                    'options' => $options_submenu
                )
            )
        );

        $options_yoast = array( // Yoast SEO plugin
            'name' => 'yoast_seo',
            'label' => 'Yoast SEO', 
            'type' => 'multicheck',
            'options' => array(
                'wpseo_author_title' => 'Title to use for Author page',
                'wpseo_author_metadesc' => 'Meta description to use for Author page',
                'wpseo_author_exclude' => 'Exclude user from Author-sitemap',
                'wpseo_keyword_analysis_disable' => 'Disable SEO analysis',
                'wpseo_content_analysis_disable' => 'Disable readability analysis'
            )
        );
        if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) {
            $settings_fields['dsbl_profile'][] = $options_yoast;
        }

        $options_um = array( // Ultimate Member plugin
            'name' => 'ultimate_member',
            'label' => 'Ultimate Member', 
            'type' => 'multicheck',
            'options' => array(
                'um_set_api_key' => 'Ultimate Member REST API',
                'um_role' => 'Community Role'
            )
        );
        if ( is_plugin_active( 'ultimate-member/index.php' ) ) {
            $settings_fields['dsbl_profile'][] = $options_um;
        }

        return $settings_fields;
    }

    function plugin_page() {
        echo '<div class="wrap">';
        // SANDBOX
        require_once dirname( __FILE__ ) . '/sandbox.php';
        // SANDBOX
        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();
        echo '</div>';
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }
        return $pages_options;
    }
}
endif;
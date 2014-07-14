<?php
/*
Plugin Name: My Fancy Option Page
Description: An example of simple option page
Version: 1.0
Author: Julien Maury
Author URI: http://tweetpressfr.github.io
*/

/*  Copyright 2014 Julien Maury  (email : contact@tweetpress.fr)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) 
    die('No !');

if( !class_exists('My_Fancy_Option_Page') ) {

    class My_Fancy_Option_Page{

        var $textdomain = 'your-textdomain';
        private $my_fancy_screen_name;
        private static $instance;
        private $settings_prefix;
        private $settings;

        /* Get Instance
        */  
        static function GetInstance()
        {
          
          
            if (!isset(self::$instance))
            {
              self::$instance = new self();
            }

            return self::$instance;
        }

        /* Constructor
        */       
        public function __construct()
        {

            $this->settings_prefix = '_mfop_';

            // start everything
            add_action('admin_init', array( $this, 'init') );

            // add menu page
            add_action('admin_menu', array( $this, 'add_menu_page') );

            // register settings
            add_action( 'admin_init' , array( $this, 'register_settings' ) );

            // Add settings link 
            add_filter( 'plugin_action_links_' . plugin_basename( $this->settings_prefix ) , array( $this, 'add_settings_link' ) );
            
        }

        /* Init
         */
        public function init()
        {
            // add fields
            $this->settings = $this->settings_fields();
        }

        /* Add menu page
         */
           public function add_menu_page()
           {

            $this->my_fancy_screen_name = 
                add_menu_page( 
                        __('My Fancy Options', $this->textdomain),
                        __('My Fancy Options', $this->textdomain), 
                        'manage_options', 
                        'plugin_settings', 
                        array($this, 'admin_page'),
                        'dashicons-admin-generic'
                );

           }

        /* Settings link for plugin
         * @return array $links
         */
           public function add_settings_link( $links ) 
           {
                $settings_link = '<a href="options-general.php?page=plugin_settings">' . __( 'Settings', $this->textdomain ) . '</a>';
                array_push( $links, $settings_link );
                return $links;
           }

        /* The settings fields
         * @return array Fields 
         */
           private function settings_fields() 
           {

                $settings['basic'] = array(
                    'title'                 => __( 'Basic settings', $this->textdomain ),
                    'description'           => __( 'My fields.', $this->textdomain ),
                    'fields'                => array(
                        array(
                            'id'            => 'text_field',
                            'label'         => __( 'Some Text' , $this->textdomain ),
                            'description'   => __( 'This is a standard text field.', $this->textdomain ),
                            'type'          => 'text',
                            'default'       => '',
                            'placeholder'   => __( 'Placeholder text', $this->textdomain )
                        ),

                        array(
                            'id'            => 'text_block',
                            'label'         => __( 'A Text Block' , $this->textdomain ),
                            'description'   => __( 'This is a standard textarea.', $this->textdomain ),
                            'type'          => 'textarea',
                            'default'       => '',
                            'placeholder'   => __( 'Placeholder text for this textarea', $this->textdomain )
                        ),
                    )
                );

                $settings = apply_filters( 'plugin_settings_fields', $settings );

                return $settings;
            }



        /* Settings section
         * @return $html
         */
        public function settings_section( $section ) 
        {
            $html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
            echo $html;
        }


        /* Register plugin settings
         * @return void
         */
        public function register_settings() 
        {
            if( is_array( $this->settings ) ) {
                foreach( $this->settings as $section => $data ) {

                    // Add section to page
                    add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), 'plugin_settings' );

                    foreach( $data['fields'] as $field ) {

                        // Validation callback for field
                        $validation = '';
                        if( isset( $field['callback'] ) ) {
                            $validation = $field['callback'];
                        }

                        // Register field
                        $option_name = $this->settings_prefix . $field['id'];
                        register_setting( 'plugin_settings', $option_name, $validation );

                        // Add field to page
                        add_settings_field( $field['id'], $field['label'], array( $this, 'display_field' ), 'plugin_settings', $section, array( 'field' => $field ) );
                    }
                }
            }
        }
        

        /* Generate HTML for displaying fields
        * @return void
        */
        public function display_field( $args ) 
        {

            $field = $args['field'];

            $html = '';

            $option_name = $this->settings_prefix . $field['id'];
            $option = get_option( $option_name );

            $data = '';
            if( isset( $field['default'] ) ) {
                $data = $field['default'];
                if( $option ) {
                    $data = $option;
                }
            }

            switch( $field['type'] ) {

                case 'text':
                    $html .= '<input id="' . esc_attr( $field['id'] ) . '" size="100" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . $data . '"/>' . "\n";
                break;

                case 'textarea':
                    $html .= '<textarea id="' . esc_attr( $field['id'] ) . '" rows="10" cols="100" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '">' . $data . '</textarea>'. "\n";
                break;

            }

           switch( $field['type'] ) {

            default:
                $html .= '<label for="' . esc_attr( $field['id'] ) . '"><span class="description">' . $field['description'] . '</span></label>' . "\n";
            break;

            }

            echo $html;
        }

        /* Adminpage display
        */
        public function admin_page()
        {
        ?>
        <div class="wrap">
            <h2 class="dashicons-before dashicons-admin-generic"> 
                <?php echo esc_html( get_admin_page_title() ); ?>
            </h2>
             <form action="options.php" method="post">
                <?php settings_fields( 'plugin_settings' ); ?>
                <?php do_settings_sections( 'plugin_settings' );?>
                <?php submit_button( __('Save'), 'primary', $this->settings_prefix.'-save-settings' ); ?>
            </form>
        </div>
        <?php
        }


    }

}

$My_Fancy_Option_Page = My_Fancy_Option_Page::GetInstance();

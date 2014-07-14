<?php
/*
Plugin Name: My Fancy Remote Call
Description: Example of remote call with WordPress
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

if ( !class_exists('My_Fancy_Remote_Call') ) 
    {
    class My_Fancy_Remote_Call
        {
        private static $instance;

        static function GetInstance()
        {
          
            if (!isset(self::$instance))
            {
              self::$instance = new self();
            }

            return self::$instance;
        }


        public function get_api_data()
            {
            $in_cache = get_site_transient( '_wordpress_org_stats' ); 

            if ( false === $in_cache )
                {
                $in_cache  = wp_remote_get( 'http://api.wordpress.org/stats/wordpress/1.0/' );
                set_site_transient( '_wordpress_org_stats', $in_cache, DAY_IN_SECONDS );
                }
            // data that matters here
            $response = wp_remote_retrieve_body( $in_cache );
            if ( 
                'OK' !== wp_remote_retrieve_response_message( $response )
                || 200 !== wp_remote_retrieve_response_code( $response )
            )
                wp_send_json_error( $response );// AJAX process

            wp_send_json_success( $response );// AJAX process
            }

        public function init()
            {
                add_action( 'wp_ajax_nopriv_get_api_data', array( $this, 'get_api_data' ) );  // do not forget to add some nonce if you rely on user inputs
                add_action( 'wp_ajax_get_api_data', array( $this, 'get_api_data' ) );
            }

        }

        $My_Fancy_Remote_Call = My_Fancy_Remote_Call::GetInstance();
        $My_Fancy_Remote_Call->init();
    }
<?php
/*
Plugin Name: My Fancy Uploads Tricks
Description: Some tips to improve uploads
Version: 1.0
Author: Julien Maury
Author URI: http://tweetpressfr.github.io
*/

/*  Copyright 2014 Julien Maury (email : contact@tweetpress.fr)

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


if( !class_exists('My_Fancy_Uploads_Tricks') )
    {
        class My_Fancy_Uploads_Tricks
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

            // Restrict uploads to a per each author view
            public function read_only_owned_files( $wp_query ) {
                if ( strpos( $_SERVER[ 'REQUEST_URI' ], '/wp-admin/upload.php' ) !== false ) {
                    if ( !current_user_can( 'level_5' ) ) {
                        global $current_user;
                        $wp_query->set( 'author', $current_user->id );
                    }
                }
            }

            // Add or remove mime types
            public function fancy_upload_mimes ( $existing_mimes = array() ) {
                 
                // your fancy file types
                $existing_mimes['doc'] = 'application/msword'; 
                $existing_mimes['pdf'] = 'application/pdf';
                $existing_mimes['zip'] = 'application/zip';
                $existing_mimes['svg'] = 'image/svg+xml';

                //delete existing mimes 
                if( isset( $existing_mimes['exe'] ) )
                    unset( $existing_mimes['exe'] );
                 
                return $existing_mimes;  
            }

            // Add column
            function add_attachments_column($columns) {

                $columns['att_weight'] = __('Size');

                return $columns;
            }

            // Add column content
            function add_attachments_column_content( $column, $attachment_id ) {


                if ( $column == 'att_weight' )
                    $size = filesize(get_attached_file($attachment_id));
                    echo size_format($size);

            }


            public function init()
            {
                add_filter('parse_query', array( $this, 'read_only_owned_files') );
                add_filter('upload_mimes', array( $this, 'fancy_upload_mimes') );

                //following hooks can be tricky, this is not "attachment" even if the post type name is attachment
                add_filter( 'manage_media_columns', array( $this, 'add_attachments_column' ) );
                add_action( 'manage_media_custom_column' , array( $this, 'add_attachments_column_content' ), 10, 2 );
            }

        }


    $My_Fancy_Uploads_Tricks = My_Fancy_Uploads_Tricks::GetInstance();
    $My_Fancy_Uploads_Tricks->init();

    }
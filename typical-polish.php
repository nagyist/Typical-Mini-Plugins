<?php
/*
Plugin Name: My Fancy Polish
Description: Just a few bunch of codes to make install prettier - but really useful in real life
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


if ( !class_exists('My_Fancy_Polish'))
    {

    class My_Fancy_Polish
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

        // Unregister some default widgets you won't use
        public function unregister_default_widgets()
            {
                unregister_widget('WP_Widget_Calendar');
                unregister_widget('WP_Widget_Recent_Comments');
                unregister_widget('WP_Widget_RSS');
                unregister_widget('WP_Widget_Archives');
                unregister_widget('WP_Widget_Meta');
                unregister_widget('WP_Widget_Tag_Cloud');
                unregister_widget('WP_Widget_Categories');
            }

        // Add CPTs in the dashboard at glance
        public function cpt_at_glance() 
        {
            $args = array(
                'public' => true,
                '_builtin' => false
            );
            $output = 'object';
            $operator = 'and';

            $post_types = get_post_types( $args, $output, $operator );
            foreach ( $post_types as $post_type ) {
                $num_posts = wp_count_posts( $post_type->name );
                $num = number_format_i18n( $num_posts->publish );
                $text = _n( $post_type->labels->singular_name, $post_type->labels->name, intval( $num_posts->publish ) );
                if ( current_user_can( 'edit_posts' ) ) {
                    $output = '<a href="edit.php?post_type=' . $post_type->name . '">' . $num . ' ' . $text . '</a>';
                    echo '<li class="post-count ' . $post_type->name . '-count">' . $output . '</li>';
                }
            }
        }

        //Delete admin nodes
        public function custom_admin_bar() 
            {   
                global $wp_admin_bar;

                if ( !current_user_can( 'administrator' ) )
                    { 

                    $wp_admin_bar->remove_menu('wp-logo');          // Remove the WordPress logo
                    $wp_admin_bar->remove_menu('about');            // Remove the about WordPress link
                    $wp_admin_bar->remove_menu('wporg');            // Remove the WordPress.org link
                    $wp_admin_bar->remove_menu('documentation');    // Remove the WordPress documentation link
                    $wp_admin_bar->remove_menu('support-forums');   // Remove the support forums link
                    $wp_admin_bar->remove_menu('feedback');         // Remove the feedback link
                    $wp_admin_bar->remove_menu('site-name');        // Remove the site name menu
                    $wp_admin_bar->remove_menu('view-site');        // Remove the view site link
                    $wp_admin_bar->remove_menu('updates');          // Remove the updates link
                    $wp_admin_bar->remove_menu('comments');         // Remove the comments link
                   // $wp_admin_bar->remove_menu('new-content');      // Remove the content link
                   // $wp_admin_bar->remove_menu('w3tc');   // w3tc menu 
                    }        
            }

        // Change spell check in post edit
        public function mce_external_languages($initArr)
            {
                $initArr['spellchecker_languages'] = '+French=fr, English=en';

                return $initArr;
            }

        // Not shield but it can be useful
        public function fake_generator()
            {
                return '<meta name="generator" content="Who knows?" />';
            }

        // Hide version and better for performance rating
        public function remove_script_version( $src )
            {
                $parts = explode( '?ver', $src );
                return $parts[0];

            }

        // Prefetch 
        public function dns_prefetch() 
            {
                echo "\n"."<!-- Prefetch -->";
                echo "\n".'<link href="//google-analytics.com" rel="dns-prefetch"/>';
                echo "\n".'<link href="//platform.twitter.com" rel="dns-prefetch"/>';
                echo "\n".'<link href="//secure.gravatar.com" rel="dns-prefetch"/>';
                echo "\n".'<link href="//0.gravatar.com" rel="dns-prefetch"/>';
                echo "\n"."<!-- /Prefetch -->"."\n";
            }


        public function init()
            {
                add_action( 'widgets_init', array( $this, 'unregister_default_widgets' ), 1);
                add_action( 'dashboard_glance_items', array( $this, 'cpt_at_glance' ) );
                add_action( 'wp_before_admin_bar_render', array( $this, 'custom_admin_bar') );
                add_filter( 'tiny_mce_before_init', array( $this, 'mce_external_languages' ) );
                add_filter( 'the_generator', array( $this, 'fake_generator' ) );
                add_filter( 'script_loader_src', array( $this, 'remove_script_version' ), 15, 1 );
                add_filter( 'style_loader_src', array( $this, 'remove_script_version' ), 15, 1 );
                add_action( 'wp_head', array( $this, 'dns_prefetch' ), 0 );
            }
        }

        $My_Fancy_Polish = My_Fancy_Polish::GetInstance();
        $My_Fancy_Polish->init();

    }
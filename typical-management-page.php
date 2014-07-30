<?php
/*
Plugin Name: My Fancy Tool Page
Description: Do fancy stuffs in tools.php
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

if( !class_exists('My_Fancy_Tool_Page') ) {

    class My_Fancy_Tool_Page{

        var $textdomain = 'your-textdomain';
        private $my_fancy_screen_name;
        private static $instance;


        static function GetInstance()
        {
          
            if (!isset(self::$instance))
            {
              self::$instance = new self();
            }

            return self::$instance;
        }

        public function init()
        {
            add_action('admin_menu', array( $this, 'add_menu_page') );
            
        }


       public function add_menu_page()
       {

        $this->my_fancy_screen_name = 
            add_management_page( 
                    __('My Fancy Tool', $this->textdomain),
                    __('My Fancy Tool', $this->textdomain), 
                    'manage_options', 
                   strtolower( __CLASS__ ), 
                    array($this, 'admin_page')
            );

       }

       public function admin_page()
       {
        ?>
        <div class="wrap">
            
        </div>
        <?php
       }


    }

}

$My_Fancy_Tool_Page = My_Fancy_Tool_Page::GetInstance();
$My_Fancy_Tool_Page->init();
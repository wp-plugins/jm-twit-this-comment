<?php
/*
Plugin Name: JM Twit This Comment
Plugin URI: http://tweetpress.fr/
Description:  Sometimes you read amazing comments that are even worthier than entire post so tweet it ^^
Author: JUlien Maury
Author URI: http://tweetpress.fr
Version: 2.0
License: GPL2++

Copyright 2015 Julien Maury

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

defined( 'ABSPATH' ) or die( 'No !' );

define( 'JM_TTC_DIR', plugin_dir_path( __FILE__ ) );
define( 'JM_TTC_URL', plugin_dir_url( __FILE__ ) );

if ( is_admin() ) {
	require( JM_TTC_DIR . 'classes/admin/admin.class.php' );
}

require( JM_TTC_DIR . 'classes/utilities.class.php' );
require( JM_TTC_DIR . 'classes/front.class.php' );

add_action( 'plugins_loaded', '_jm_ttc_init' );
function _jm_ttc_init() {

	// plugin textdomain
	load_plugin_textdomain( 'jm-ttc', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	if ( is_admin() ) {
		new TokenToMe\tweet_this\Admin();
	}

	new TokenToMe\tweet_this\Front();
}



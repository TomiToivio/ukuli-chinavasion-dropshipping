<?php
/*
Plugin Name: Ukuli Chinavasion Dropshipping
Plugin URI: https://ukuli.fi
Description: Chinavasion Dropshipping for WooCommerce
Version: 1.0.1
Author: TomiToivio
Author URI: https://ukuli.fi
License: GPLv2 or later
Text Domain: ukuli
Domain Path: /languages
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2019 Ukuli Data
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$plugin_dir = basename( dirname( __FILE__ ) );
load_plugin_textdomain( 'ukuli', null, $plugin_dir . "/languages");

/**
 * Require plugin files.
 */
require(plugin_dir_path( __FILE__ ) . 'classes/class-ukuli-chinavasion.php');
require(plugin_dir_path( __FILE__ ) . 'lib/ukuli-ajax.php');
require(plugin_dir_path( __FILE__ ) . 'lib/ukuli-cron.php');
require(plugin_dir_path( __FILE__ ) . 'lib/ukuli-database.php');
require(plugin_dir_path( __FILE__ ) . 'lib/ukuli-hooks.php');
require(plugin_dir_path( __FILE__ ) . 'lib/ukuli-notices.php');
require(plugin_dir_path( __FILE__ ) . 'lib/ukuli-order.php');
require(plugin_dir_path( __FILE__ ) . 'lib/ukuli-product.php');

if ( is_admin() ) {
   require(plugin_dir_path( __FILE__ ) . 'admin/ukuli-admin.php');
}

register_deactivation_hook( __FILE__, 'ukuli_chinavasion_remove_database' );
register_activation_hook( __FILE__, 'ukuli_chinavasion_database_install' );

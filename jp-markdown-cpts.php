<?php
/**
 * Plugin Name: VA JP Markdown CPTs
 * Plugin URI: https://github.com/visualive/va-jp-markdown-cpts
 * Description: Plugin that make markdown module of the Jetpack correspond with Custom post types..
 * Author: KUCKLU
 * Version: 1.0.0
 * WordPress Version: 4.5
 * PHP Version: 5.4
 * DB Version: 1.0.0
 * Author URI: https://www.visualive.jp
 * Domain Path: /langs
 * Text Domain: va-jpmd-cpts
 * Prefix: va_jpmd_cpts_
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package    WordPress
 * @subpackage VA JP Markdown CPTs
 * @since      1.0.0
 * @author     KUCKLU <kuck1u@visualive.jp>
 *             Copyright (C) 2017 KUCKLU & VisuAlive.
 *             This program is free software; you can redistribute it and/or modify
 *             it under the terms of the GNU General Public License as published by
 *             the Free Software Foundation; either version 2 of the License, or
 *             (at your option) any later version.
 *             This program is distributed in the hope that it will be useful,
 *             but WITHOUT ANY WARRANTY; without even the implied warranty of
 *             MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *             GNU General Public License for more details.
 *             You should have received a copy of the GNU General Public License along
 *             with this program; if not, write to the Free Software Foundation, Inc.,
 *             51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 *             It is also available through the world-wide-web at this URL:
 *             http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( __FILE__ ) . '/incs/trait-instance.php';
require_once dirname( __FILE__ ) . '/incs/class-admin.php';
require_once dirname( __FILE__ ) . '/incs/class-markdown.php';

if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'markdown' ) ) {
	add_action( 'plugins_loaded', array( \VAJPMDCPTS\Admin::get_instance(), 'load' ) );
	add_action( 'init', array( \VAJPMDCPTS\Markdown::get_instance(), 'load' ), 15 );
}

/**
 * Run uninstall.
 */
register_activation_hook( __FILE__, function () {
	register_uninstall_hook( __FILE__, '_vajpcpts_uninstall' );
} );

/**
 * Run uninstall [Debug mode].
 */
if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
	register_deactivation_hook( __FILE__, '_vajpcpts_uninstall' );
}

/**
 * Uninstall.
 */
function _vajpcpts_uninstall() {
	delete_option( 'va_jpmd_cpts' );
}

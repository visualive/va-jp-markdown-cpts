<?php
/**
 * WordPress plugin admin class.
 *
 * @package    Jetpack
 * @subpackage VA Jp Markdown CPTs
 * @since      1.0.0
 * @author     KUCKLU <kuck1u@visualive.jp>
 *             Copyright (C) 2017 KUCKLU and VisuAlive.
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

namespace VAJPMDCPTS;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Admin
 *
 * @package VAJPMDCPTS
 */
class Admin {
	use Instance;

	const PLUGIN_PREFIX = 'va_jpmd_cpts_';
	const OPTION_NAME = 'va_jpmd_cpts';

	/**
	 * This hook is called once any activated plugins have been loaded.
	 */
	public function __construct() {
	}

	/**
	 * Kicks things off on `init` action
	 */
	public function load() {
		add_action( 'admin_init', [ &$this, 'admin_init' ] );
		add_action( 'jetpack_admin_menu', [ &$this, 'admin_menu' ], 15 );
		add_action( 'get_post_metadata', [ &$this, 'get_post_metadata' ], 10, 3 );
	}

	/**
	 * Get a list of all registered post type objects.
	 *
	 * @return array
	 */
	public static function get_post_types() {
		$builtin = apply_filters( 'vajpmdcpts_builtin_post_types', [
			'post' => 'post',
			'page' => 'page',
		] );
		$cpts    = get_post_types( [
			'_builtin' => false,
		] );
		$cpts    = array_merge( $builtin, $cpts );

		return apply_filters( 'vajpmdcpts_get_post_types', $cpts );
	}

	/**
	 * Add admin menu.
	 */
	public function admin_menu() {
		add_submenu_page(
			'jetpack',
			__( 'Jp Markdown CPTs', 'va-jpmd-cpts' ),
			__( 'Jp Markdown CPTs', 'va-jpmd-cpts' ),
			'manage_options',
			'va-jpmd-cpts',
			[ &$this, '_options_page' ]
		);
	}

	/**
	 * Render setting form and register option.
	 */
	public function admin_init() {
		register_setting(
			self::PLUGIN_PREFIX . 'settings',
			self::OPTION_NAME,
			[ &$this, '_sanitize' ]
		);

		add_settings_section( self::PLUGIN_PREFIX . 'section', null, null, self::PLUGIN_PREFIX . 'settings' );

		add_settings_field(
			self::OPTION_NAME,
			esc_html__( 'Post type choices', 'va-jpmd-cpts' ),
			[ &$this, '_render_custom_post_types' ],
			self::PLUGIN_PREFIX . 'settings',
			self::PLUGIN_PREFIX . 'section'
		);
	}

	/**
	 * Get meta data.
	 *
	 * @param null   $value     The value get_metadata() should return - a single metadata value, or an array of values.
	 * @param int    $object_id Post ID.
	 * @param string $meta_key  Meta key.
	 *
	 * @return bool|null
	 */
	public function get_post_metadata( $value, $object_id, $meta_key ) {
		if ( '_wpcom_is_markdown' === $meta_key ) {
			$optn  = get_option(
				self::OPTION_NAME,
				apply_filters( 'vajpmdcpts_default_option', [ 'post', 'jetpack-portfolio' ] )
			);
			$_post = get_post( $object_id );

			if ( ! in_array( $_post->post_type, $optn ) ) {
				$value = false;
			}
		}

		return $value;
	}

	/**
	 * Render form.
	 */
	public static function _render_custom_post_types() {
		$cpts   = self::get_post_types();
		$optn   = get_option(
			self::OPTION_NAME,
			apply_filters( 'vajpmdcpts_default_option', [ 'post', 'jetpack-portfolio' ] )
		);
		$output = [];

		foreach ( $cpts as $type ) {
			$data     = get_post_type_object( $type );
			$output[] = '<ul>';
			$output[] = '<li><label>';
			$output[] = '<input id="' . esc_attr( self::PLUGIN_PREFIX . $type ) . '" type="checkbox" name="' . esc_attr( self::OPTION_NAME ) . '[]" value="' . esc_attr( $type ) . '" ' . checked( in_array( $type, $optn ), true, false ) . '>';
			$output[] = esc_html( $data->labels->name );
			$output[] = '</label></li>';
			$output[] = '</ul>';
		}

		echo implode( PHP_EOL, $output );
	}

	/**
	 * Create option page.
	 */
	public function _options_page() {
		?>
		<div class="wrap">
			<h1><?php _e( 'Jp Markdown CPTs', 'va-jpmd-cpts' ); ?></h1>

			<form action="options.php" method="post">
				<?php
				settings_fields( self::PLUGIN_PREFIX . 'settings' );
				do_settings_sections( self::PLUGIN_PREFIX . 'settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Sanitize.
	 *
	 * @param array $options Settings.
	 *
	 * @return array
	 */
	public static function _sanitize( $options = array() ) {
		array_filter( $options, 'sanitize_key' );

		return $options;
	}
}

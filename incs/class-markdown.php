<?php
/**
 * WordPress plugin WPCom markdown support class.
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
 * Class Markdown_Supports
 *
 * @package VAJPMDCPTS
 */
class Markdown {
	use Instance;

	const POST_TYPE_SUPPORT = 'wpcom-markdown';
	const OPTION_NAME = 'va_jpmd_cpts';

	/**
	 * This hook is called once any activated plugins have been loaded.
	 */
	protected function __construct() {
	}

	/**
	 * Kicks things off on `init` action
	 */
	public function load() {
		$this->add_post_type_support();
	}

	/**
	 * We don't want Markdown conversion all over the place.
	 *
	 * @todo: How about performance?
	 */
	public function add_post_type_support() {
		$optn = get_option(
			self::OPTION_NAME,
			apply_filters( 'vajpmdcpts_default_option', [ 'post', 'jetpack-portfolio' ] )
		);
		$cpts = self::get_post_types();

		foreach ( $cpts as $type ) {
			if ( false !== array_search( $type, $optn ) ) {
				if ( false === self::post_type_supports( $type ) ) {
					add_post_type_support( $type, self::POST_TYPE_SUPPORT );
				}
			} else {
				remove_post_type_support( $type, self::POST_TYPE_SUPPORT );
			}
		}
	}

	/**
	 * Get a list of all registered post type objects.
	 *
	 * @return array
	 */
	public function get_post_types() {
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
	 * Check a post type's support for WPCom Markdown.
	 *
	 * @param string $post_type The post type being checked.
	 *
	 * @return bool
	 */
	public function post_type_supports( $post_type ) {
		if ( is_null( $post_type ) ) {
			$post_type = 'post';
		} else {
			$post_type = sanitize_key( $post_type );
		}

		return post_type_supports( $post_type, self::POST_TYPE_SUPPORT );
	}

	/**
	 * Retrieves a list of post type names that support for WPCom Markdown.
	 *
	 * @return array
	 */
	public function get_post_types_by_support() {
		$supports = get_post_types_by_support( self::POST_TYPE_SUPPORT );

		if ( false !== ( $revision_key = array_search( 'revision', $supports ) ) ) {
			unset( $supports[ $revision_key ] );

			if ( empty( $supports ) ) {
				$supports = array_values( $supports );
			}
		}

		return $supports;
	}
}

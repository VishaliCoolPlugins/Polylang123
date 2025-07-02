<?php
/**
 * @package Linguator
 */

/**
 * A class to manage specific compatibility issue with cache plugins
 * Tested with WP Rocket 2.10.7
 *
 * @since 2.3
 */
class LMAT_Cache_Compat {
	/**
	 * Setups actions
	 *
	 * @since 2.3
	 */
	public function init() {
		if ( LMAT_COOKIE ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'add_cookie_script' ) );
		}

		// Since version 3.0.5, WP Rocket does not serve the cached page if our cookie is not set
		if ( ! defined( 'WP_ROCKET_VERSION' ) || version_compare( WP_ROCKET_VERSION, '3.0.5', '<' ) ) {
			add_action( 'wp', array( $this, 'do_not_cache_site_home' ) );
		}

		add_action( 'clean_post_cache', array( $this, 'clean_post_cache' ), 1 );
	}

	/**
	 * Currently all tested cache plugins don't send cookies with cached pages.
	 * This makes us impossible to know the language of the last browsed page.
	 * This functions allows to create the cookie in javascript as a workaround.
	 *
	 * @since 2.3
	 *
	 * @return void
	 */
	public function add_cookie_script() {
		// Embeds should not set the cookie.
		if ( is_embed() ) {
			return;
		}

		$domain   = ( 2 === LMAT()->options['force_lang'] ) ? wp_parse_url( LMAT()->links_model->home, PHP_URL_HOST ) : COOKIE_DOMAIN;
		$samesite = ( 3 === LMAT()->options['force_lang'] ) ? 'None' : 'Lax';

		/** This filter is documented in include/cookie.php */
		$expiration = (int) apply_filters( 'lmat_cookie_expiration', YEAR_IN_SECONDS );

		if ( 0 !== $expiration ) {
			$format = 'var expirationDate = new Date();
				expirationDate.setTime( expirationDate.getTime() + %7$d * 1000 );
				document.cookie = "%1$s=%2$s; expires=" + expirationDate.toUTCString() + "; path=%3$s%4$s%5$s%6$s";';
		} else {
			$format = 'document.cookie = "%1$s=%2$s; path=%3$s%4$s%5$s%6$s";';
		}

		$js = sprintf(
			"(function() {
				{$format}
			}());\n",
			esc_js( LMAT_COOKIE ),
			esc_js( lmat_current_language() ),
			esc_js( COOKIEPATH ),
			$domain ? '; domain=' . esc_js( $domain ) : '',
			is_ssl() ? '; secure' : '',
			'; SameSite=' . $samesite,
			esc_js( $expiration )
		);

		// Need to register prior to enqueue empty script and add extra code to it.
		wp_register_script( 'lmat_cookie_script', '', array(), LINGUATOR_VERSION, true );
		wp_enqueue_script( 'lmat_cookie_script' );
		wp_add_inline_script( 'lmat_cookie_script', $js );
	}

	/**
	 * Informs cache plugins not to cache the home in the default language
	 * When the detection of the browser preferred language is active
	 *
	 * @since 2.3
	 */
	public function do_not_cache_site_home() {
		if ( ! defined( 'DONOTCACHEPAGE' ) && LMAT()->options['browser'] && LMAT()->options['hide_default'] && is_front_page() && lmat_current_language() === lmat_default_language() ) {
			define( 'DONOTCACHEPAGE', true );
		}
	}

	/**
	 * Allows cache plugins to clean the right post type archive cache when cleaning a post cache.
	 *
	 * @since 3.0.5
	 *
	 * @param int $post_id Post id.
	 */
	public function clean_post_cache( $post_id ) {
		$lang = LMAT()->model->post->get_language( $post_id );

		if ( $lang ) {
			$filter_callback = function ( $link, $post_type ) use ( $lang ) {
				return lmat_is_translated_post_type( $post_type ) && 'post' !== $post_type ? LMAT()->links_model->switch_language_in_link( $link, $lang ) : $link;
			};
			add_filter( 'post_type_archive_link', $filter_callback, 99, 2 );
		}
	}
}

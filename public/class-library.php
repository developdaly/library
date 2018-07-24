<?php
/**
 * Library.
 *
 * @package   Library
 * @author    Patrick Daly <patrick@developdaly.com>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/library
 * @copyright 2014 Patrick Daly
 */

/**
 * Library class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-library-admin.php`
 *
 * @package Library
 * @author  Patrick Daly <patrick@developdaly.com>
 */
class Library {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.1.0';

	/**
	 * Transient key used to store the latest modified date of published Library Term data.
	 * This key is used for cache-busting of existing transient keys when a Library Term is updated.
	 * 
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */const LIBRARY_LAST_MODIFIED = 'LIBRARY_LAST_MODIFIED';

	/**
	 *
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */

	protected $plugin_slug = 'library';
	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Add the library shortcode
		add_shortcode( 'library', array( $this, 'shortcode' ) );

		// Verify or create last Offer edit date for caching
		add_action( 'wp_loaded', array( $this, 'ensure_library_last_modified_transient_key_exists' ) );

		// Invalidate cache when an Offer is created or edited
		add_action( 'save_post', array( $this, 'update_library_last_modified_transient_key' ) );
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Access the terms with a shortcode.
	 *
	 * @since    1.0.0
	 */
	public function shortcode( $atts ) {
		global $post;

		$library_last_modified_key = get_transient( self::LIBRARY_LAST_MODIFIED );

		$cacheKey = 'library-'  . $atts['term'] . '-' . $library_last_modified_key;

		$cached = get_transient( $cacheKey );

		if ( false === $cached ) {
			$args = array(
				'name' => $atts['term'],
				'post_type' => 'library_term',
			);

			$query = new WP_Query( $args );

			$cached = '';

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					$cached = '<!--' . $cacheKey . '-->' . get_the_content();
				}
			}

			set_transient( $cacheKey, $cached );

			wp_reset_postdata();
		}

		return $cached;
	}

	function ensure_library_last_modified_transient_key_exists() {
		$library_last_modified_key = get_transient( self::LIBRARY_LAST_MODIFIED );

		if ( false === $library_last_modified_key ) {
			// No stored timestamp - find the last modified Offer time and store it
			$args = array(
				'post_type' => 'library_term',
				'post_status' => 'publish',
				'orderby' => 'modified',
				'order' => 'desc',
				'posts_per_page' => 1,
			);
			$latest_modified_library_post = new WP_Query( $args );

			if ( $latest_modified_library_post->have_posts() ) :
				$latest_modified_library_post->the_post();
				$library_last_modified_key = strtotime( get_the_modified_date( 'c' ) );
			endif;

			set_transient( self::LIBRARY_LAST_MODIFIED, $library_last_modified_key  );
		}
	}

	// Create new timestamp for cache key when an Library is saved.
	function update_library_last_modified_transient_key( $post_id ) {
		$edited_post = get_post( $post_id );

		// Test to see if an Library post was updated
		if ( 'library_term' !== $edited_post->post_type ) {
			return;
		}

		// A Library Term post was saved, store new timestamp for this post
		set_transient( self::LIBRARY_LAST_MODIFIED, strtotime( $edited_post->post_modified ), 0 );
	}
}

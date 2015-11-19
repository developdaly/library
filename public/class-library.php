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
	const VERSION = '1.0.0';

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

		// Clears the library shortcode cache on content change
		add_action( 'save_post', array( $this, 'clear_cache_on_library_save' ) );
		// TODO Investigate using a custom action specific to the custom post type http://stackoverflow.com/a/6270232

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

		$cacheKey = 'library_term_' . $atts['term'];

		$query = wp_cache_get( $cacheKey );

		if ( $query === false ) {
			$args = array(
				'name' => $atts['term'],
				'post_type' => 'library_term',
			);

			$query = new WP_Query( $args );

			wp_cache_set( $cacheKey, $query );

		}

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$output = get_the_content();
			}
		}

		wp_reset_postdata();

		if ( ! empty( $output ) ) {
			return do_shortcode( $output );
		}
	}

	/**
	* Clears the shortcode cache on content change.
	*/
	public function clear_cache_on_library_save( $post_id ) {
		if ( ( ! wp_is_post_autosave( $post_id ) ) && ( get_post_status( $post_id ) === 'publish' ) ) {
			$post = get_post( $post_id );
			$cacheKey = 'library_term_' . $post->post_name;
			wp_cache_delete( $cacheKey );
		}
	}
}

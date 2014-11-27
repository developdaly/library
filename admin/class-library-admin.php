<?php
/**
 * Library.
 *
 * @package   Library_Admin
 * @author    Patrick Daly <patrick@developdaly.com>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/library
 * @copyright 2014 Patrick Daly
 */

/**
 * Library_Admin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-library.php`
 *
 * @package Library_Admin
 * @author  Patrick Daly <patrick@developdaly.com>
 */
class Library_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		$plugin = Library::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		add_action( 'init', array( $this, 'register' ) );

		/* Fire our meta box setup function on the post editor screen. */
		add_action( 'load-post.php',		array( $this, 'library_meta_boxes_setup' ) );

		add_action( 'media_buttons', array( $this, 'add_form_button' ), 20 );

		add_action( 'admin_footer',  array( $this, 'add_mce_popup' ) );

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

	public function register() {

		$labels = array(
			'name' => _x( 'Terms', 'shortcode terms general name', 'library' ),
			'singular_name' => _x( 'Term', 'shortcode term singular name', 'library' ),
			'add_new' => _x( 'Add New', 'shortcode term', 'library' ),
			'add_new_item' => __( 'Add New Term', 'library' ),
			'edit_item' => __( 'Edit Term', 'library' ),
			'new_item' => __( 'New Term', 'library' ),
			'view_item' => __( 'View Term', 'library' ),
			'search_items' => __( 'Search Terms', 'library' ),
			'not_found' => __( 'No terms found', 'library' ),
			'not_found_in_trash' => __( 'No terms found in Trash', 'library' ),
			'parent_item_colon' => __( 'Parent Term:', 'library' ),
			'menu_name' => _x( 'Library', 'shortcode term collection', 'library' ),
		);

		$args = array(
			'labels' => $labels,
			'supports' => array( 'title', 'editor', 'revisions' ),
			'public' => false, // hide the front end UI
			'show_ui' => true, // keep admin UI
			'show_in_nav_menus' => true,
			'menu_position' => 5,
		);

		register_post_type( 'library_term', $args );
	}

	/**
	 * Meta box setup.
	 *
	 * @since     1.0.0
	 */
	public function library_meta_boxes_setup() {

		/* Add meta boxes on the 'add_meta_boxes' hook. */
		add_action( 'add_meta_boxes', array( $this, 'library_add_post_meta_boxes' ) );
	}

	/**
	 * Creates the meta box.
	 *
	 * @since     1.0.0
	 */
	public function library_add_post_meta_boxes() {

		add_meta_box(
			'library',
			esc_html__( 'How to Use this Term', 'library' ),
			array( $this, 'library_class_meta_box' ),
			'library_term',
			'normal',
			'high'
		);
	}

	/**
	 * Display the meta box.
	 *
	 * @since     1.0.0
	 */
	public function library_class_meta_box( $object, $box ) {
		global $post;

		$shortcode_example = '<code>[library term="' . $post->post_name . '"]</code>';
		$php_example = '<code>&lt;?php echo do_shortcode( \'[library term="' . $post->post_name . '"]\' ) ?&gt;</code>';
		echo '<p>';
		printf( __( 'To display this inside your content use %s OR to use this inside of a template file use %s', 'library' ), $shortcode_example, $php_example );
		echo '</p><p>';
		_e( 'You can change the term slug by editing the permalink slug underneath the title.', 'library' );
		echo '</p>';

	}

	/**
	 * Checks to see if we are editing or adding a post.
	 *
	 * @return bool
	 * @since  1.0.3
	 */
	public static function page_supports_add_form_button() {
		// check we aren't calling this too early
		if ( ! function_exists( 'get_current_screen' ) ) {
			return false;
		}

		$screen = get_current_screen();
		$is_post_edit_page = in_array( $screen->parent_base, array( 'edit' ) );

		if ( 'library_term' == $screen->post_type ) {
			$is_post_edit_page = false;
		}

		$display_add_form_button = apply_filters( 'library_display_add_form_button', $is_post_edit_page );

		return $display_add_form_button;
	}

	/**
	 * Adds a button to the editor
	 *
	 * @since 1.0.3
	 */
	public function add_form_button() {
		$is_add_form_page = self::page_supports_add_form_button();
		if ( ! $is_add_form_page ) {
			return;
		}

		// do a version check for the new 3.5 UI
		$version = get_bloginfo( 'version' );

		if ( $version < 3.5 ) {
			// show button for v 3.4 and below
			echo '<a href="#TB_inline?width=480&inlineId=select_library_shortcode" class="thickbox" id="add_gform">' . __( 'Add Library Shortcode', 'library' ) . '</a>';
		} else {
			// display button matching new UI
			echo '<a href="#TB_inline?width=480&inlineId=select_library_shortcode" class="thickbox button library_media_link" id="add_library_shortcode" title="' . __( 'Add Library Shortcode', 'library' ) . '">' . __( 'Add Library Shortcode', 'library' ) . '</a>';
		}
	}

	/**
	 * Add the form for inserting shortcodes into posts
	 *
	 * @since 1.0.3
	 */
	public static function add_mce_popup() {
		if ( ! self::page_supports_add_form_button() ) {
			return;
		}
		?>
		<script>
			function library_insert_shortcode() {
				var term_slug = jQuery( '#add_term_slug' ).val();
				if ( '' === term_slug ) {
					alert( '<?php _e( 'Please select a shortcode', 'library' ); ?>' );
					return;
				}

				window.send_to_editor( '[library term="' + term_slug + '"]' );
			}
		</script>

		<div id="select_library_shortcode" style="display:none;">
			<div class="wrap">
				<h3><?php _e( 'Insert A Term', 'library' ); ?></h3>
				<p>
					<?php _e( 'Select a term below to add it to your post or page.', 'library' ); ?>
				</p>
				<p>
					<select id="add_term_slug">
						<option value=""><?php _e( 'Select a Term', 'library' ); ?></option>
						<?php
							// TODO: what happens when there are 1000 terms? AJAX list with a short fallback
							$args = array(
								'post_type' => 'library_term',
								'order'     => 'ASC',
								'orderby'   => 'title',
							);
							$query = new WP_Query( $args );

							if ( $query->have_posts() ) {
								global $post;
								while ( $query->have_posts() ) {
									$query->the_post();
								?>
								<option value="<?php echo esc_attr( $post->post_name ); ?>"><?php the_title(); ?></option>
								<?php
								}
							}
							wp_reset_postdata();
						?>
					</select> <br/>
				</p>
				<p>
					<input type="button" class="button button-primary" value="<?php esc_attr_e( 'Insert Shortcode', 'library' ); ?>" onclick="library_insert_shortcode();"/>&nbsp;&nbsp;&nbsp;
					<a class="button" onclick="tb_remove(); return false;"><?php esc_attr_e( 'Cancel', 'library' ); ?></a>
				</p>
			</div>
		</div>

		<?php
	}


}

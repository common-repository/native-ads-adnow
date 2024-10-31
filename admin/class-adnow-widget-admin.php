<?php
/**
 * Adnow Widget Admin class
 *
 * @file
 * @package Adnow Widget
 * @phpcs:disable WordPress.DB.DirectDatabaseQuery
 * @phpcs:disable WordPress.PHP.DiscouragedPHPFunctions
 */

/** Define('API_URL', 'http://host.docker.internal:8080/wp_aadb.php'); **/
/** Define('API_URL', 'http://127.0.0.1/wp_aadb.php'); **/
define('AWP_API_URL', 'https://wp_plug.adnow.com/wp_aadb.php');

/**
 * Adnow Widget Admin class
 */
class Adnow_Widget_Admin {

	/**
	 * Plugin name
	 *
	 * @var string $plugin_name
	 */
	private $plugin_name;

	/**
	 * Token
	 *
	 * @var string $token
	 */
	public $token;

	/**
	 * Message error
	 *
	 * @var string $message_error
	 */
	public $message_error = '';

	/**
	 * Widgets
	 *
	 * @var object $widgets
	 */
	public $widgets;

	/**
	 * Option name
	 *
	 * @var string $option_name
	 */
	private $option_name = 'Adnow_Widget';

	/**
	 * Version
	 *
	 * @var string $version
	 */
	private $version;

	/**
	 * Aadb getter
	 *
	 * @var object $aadb_getter
	 */
	private $aadb_getter;

	/**
	 * Constructor
	 *
	 * @param mixed $plugin_name Plugin name.
	 * @param mixed $version Version.
	 * @param mixed $aadb_getter Aadb getter.
	 */
	public function __construct( $plugin_name, $version, $aadb_getter ) {
		$this->aadb_getter = $aadb_getter;
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->token       = false;
		$options_token     = get_option( $this->option_name . '_key' );
		if ( ! empty( $options_token ) ) {
			$widgets_val = $this->aadb_getter->get( $options_token );
			if ( false === $widgets_val['validate'] ) {
				$this->message_error = 'You have entered an invalid token!';
			}
			$this->token = $options_token;
		}
	}

	/**
	 * Enqueue styles
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/adnow-widget-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Enqueue scripts
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/adnow-widget-admin.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Add options page
	 */
	public function add_options_page() {
		$this->plugin_screen_hook_suffix = add_menu_page(
			__( 'Edit place', 'adnow-widget' ),
			__( 'Adnow Plugin', 'adnow-widget' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_options_page' )
		);
	}

	/**
	 * Make menu
	 */
	public function make_menu() {
		add_submenu_page( $this->plugin_name, 'Edit place', 'Edit place', 'manage_options', 'edit_place', array( $this, 'setting_adnow' ) );
	}

	/**
	 * Setting adnow
	 */
	public function setting_adnow() {
		include_once 'partials/class-adnow-widget-area.php';
	}

	/**
	 * Display options page
	 */
	public function display_options_page() {
		include_once 'partials/adnow-widget-admin-display.php';
	}

	/**
	 * Register setting
	 */
	public function register_setting() {
		add_settings_section(
			$this->option_name . '_general',
			'',
			array( $this, $this->option_name . '_general_cb' ),
			$this->plugin_name
		);

		add_settings_section(
			$this->option_name . '_head_account_key',
			'',
			array(),
			$this->plugin_name
		);

		add_settings_section(
			$this->option_name . '_impressions',
			'',
			array( $this, $this->option_name . '_impressions_cb' ),
			$this->plugin_name
		);

		add_settings_section(
			$this->option_name . '_turn',
			'',
			array( $this, $this->option_name . '_turn_cb' ),
			$this->plugin_name
		);

		register_setting( $this->plugin_name, $this->option_name . '_general' );
		register_setting( $this->plugin_name, $this->option_name . '_key' );
		register_setting( $this->plugin_name, $this->option_name . '_turn' );
	}

	/**
	 * Adnow Widget general cb
	 */
	public function adnow_widget_general_cb() {
		if ( false !== $this->token ) {
			$this->widgets = $this->aadb_getter->get( $this->token );
			$account_id    = ! empty( $this->widgets['account']['id'] ) ? $this->widgets['account']['id'] : '';
			$account_email = ! empty( $this->widgets['account']['email'] ) ? $this->widgets['account']['email'] : '';
		} ?>
			<div class="account display_block">
				<div class="title">Account</div>
				<div class="text">
				<p><b>Token</b><input autocomplete="off" type="text" name="<?php echo esc_html( $this->option_name ); ?>_key" id="<?php echo esc_html( $this->option_name ); ?>_key" value="<?php echo esc_html( $this->token ); ?>"><span class="message_error"><?php echo esc_html( $this->message_error ); ?></span></p>
				<?php if ( false !== $this->token && '' === $this->message_error ) : ?>
					<p><b>ID</b> <span><?php echo esc_html( $account_id ); ?></span></p>
					<p><b>E-mail</b> <span><?php echo esc_html( $account_email ); ?></span></p>
					<p><a href="https://adnow.com/" class="site" target="_blank">adnow.com</a> <span><a href="https://adnow.com/" class="help">Help</a></span></p>
					<div class="submit_cover success"><a href="<?php echo esc_html( admin_url() ); ?>admin.php?page=edit_place" class="submit">Manage Places</a></div>
				<?php else : ?>
					<input class="checkbox" autocomplete="off" type="hidden" name="<?php echo esc_html( $this->option_name ) . '_turn'; ?>" id="<?php echo esc_html( $this->option_name ) . '_turn'; ?>" value="before"><br>

				<?php endif; ?>
				</div>
			</div>
			<?php
	}

	/**
	 * Adnow Widget turn cb
	 */
	public function adnow_widget_turn_cb() {
		$turn = get_option( $this->option_name . '_turn' );
		?>
		<?php if ( false !== $this->token && '' === $this->message_error ) : ?>
		<div class="display_block adblock">
			<div class="title">Antiadblock</div>
			<div class="text">
				<div class="checkbox_cover <?php echo ! empty( $turn ) ? 'success' : ''; ?>">
					<label>
						<input class="checkbox" type="checkbox" name="<?php echo esc_html( $this->option_name ) . '_turn'; ?>" id="<?php echo esc_html( $this->option_name ) . '_turn'; ?>" value="before" <?php checked( $turn, 'before' ); ?>>
						<span class="check"><i></i></span>
						<span class="name">Activate Adblock</span>
					</label>
				</div>
			</div>
		</div>
			<?php
		endif;
	}

	/**
	 * Adnow Widget impressions cb
	 */
	public function adnow_widget_impressions_cb() {
		if ( false !== $this->token && '' === $this->message_error ) {
			$impressions = ! empty( $this->widgets['impressions'] ) ? $this->widgets['impressions'] : 0;
			$impressions = number_format( $impressions, 0, '', ' ' );
		}
		?>
		<?php if ( false !== $this->token && '' === $this->message_error ) : ?>
		<div class="display_block stats">
			<div class="title">Antiadblock stats for today</div>
			<div class="text">
				<div class="adn_name">Impressions</div>
				<div class="value"><?php echo esc_html( $impressions ); ?></div>
			</div>
		</div>
			<?php
		endif;
	}

}

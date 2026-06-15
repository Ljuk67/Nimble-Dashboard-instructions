<?php
/**
 * Main plugin class.
 *
 * @package NimbleDashboardInstructions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Nimble_Dashboard_Instructions {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	const VERSION = '1.0.0';

	/**
	 * Plugin text domain.
	 *
	 * @var string
	 */
	const TEXT_DOMAIN = 'nimble-dashboard-instructions';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'load-index.php', array( $this, 'configure_dashboard_banner' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'admin_notices', array( $this, 'render_media_notice' ) );
	}

	/**
	 * Load plugin translations.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( self::TEXT_DOMAIN, false, dirname( plugin_basename( __DIR__ ) ) . '/languages' );
	}

	/**
	 * Render the banner above the main dashboard content.
	 *
	 * @return void
	 */
	public function configure_dashboard_banner() {
		add_action( 'in_admin_header', array( $this, 'render_dashboard_banner' ), 1 );
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @return void
	 */
	public function enqueue_admin_assets() {
		wp_enqueue_style(
			'nimble-dashboard-instructions-admin',
			plugin_dir_url( dirname( __FILE__ ) ) . 'assets/css/admin.css',
			array(),
			$this->get_asset_version( 'assets/css/admin.css' )
		);

		wp_enqueue_script(
			'nimble-dashboard-instructions-admin',
			plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/admin.js',
			array(),
			$this->get_asset_version( 'assets/js/admin.js' ),
			true
		);

		wp_localize_script(
			'nimble-dashboard-instructions-admin',
			'nimbleDashboardInstructions',
			array(
				'mediaNoticeBeforeLink' => __(
					'Use PNG only for transparent images. For regular photos, use .jpeg, .jpg, or .webp. Before uploading images, always optimize them with ',
					self::TEXT_DOMAIN
				),
				'mediaNoticeAfterLink'  => __(
					' and keep every image under 0.5 MB / 500 KB.',
					self::TEXT_DOMAIN
				),
				'tinypngUrl'            => esc_url( 'https://tinypng.com' ),
				'tinypngLabel'          => esc_html__( 'tinypng.com', self::TEXT_DOMAIN ),
			)
		);
	}

	/**
	 * Render dashboard banner.
	 *
	 * @return void
	 */
	public function render_dashboard_banner() {
		if ( ! $this->is_dashboard_screen() ) {
			return;
		}

		$instruction_items = apply_filters(
			'nimble_dashboard_instructions_items',
			array(
				__( 'Use PNG only when the image needs transparency. For regular photos, use .jpeg, .jpg, or .webp.', self::TEXT_DOMAIN ),
				sprintf(
					/* translators: %s is a TinyPNG hyperlink. */
					__( 'Before uploading images, optimize them with %s.', self::TEXT_DOMAIN ),
					$this->get_tinypng_link_html()
				),
				__( 'Keep every uploaded image under 0.5 MB / 500 KB.', self::TEXT_DOMAIN ),
				__( 'After editing important pages, check the result on desktop and mobile before publishing.', self::TEXT_DOMAIN ),
			)
		);

		?>
		<div class="notice nimble-dashboard-instructions-banner">
			<div class="nimble-dashboard-instructions-banner__content">
				<div class="nimble-dashboard-instructions-banner__eyebrow">
					<?php esc_html_e( 'Nimble.Help client instructions', self::TEXT_DOMAIN ); ?>
				</div>
				<h1 class="nimble-dashboard-instructions-banner__title">
					<?php esc_html_e( 'Before uploading images or editing content', self::TEXT_DOMAIN ); ?>
				</h1>
				<p class="nimble-dashboard-instructions-banner__lead">
					<?php esc_html_e( 'These rules keep the website fast, consistent, and easy to maintain.', self::TEXT_DOMAIN ); ?>
				</p>
				<ul class="nimble-dashboard-instructions-banner__list">
					<?php foreach ( $instruction_items as $item ) : ?>
						<li><?php echo wp_kses( $item, $this->get_allowed_notice_html() ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		<?php
	}

	/**
	 * Render media-library notice.
	 *
	 * @return void
	 */
	public function render_media_notice() {
		if ( ! $this->is_media_screen() ) {
			return;
		}

		?>
		<div class="notice notice-warning nimble-dashboard-instructions-media-notice">
			<p>
				<strong><?php esc_html_e( 'Important:', self::TEXT_DOMAIN ); ?></strong>
				<?php echo wp_kses( $this->get_media_notice_html(), $this->get_allowed_notice_html() ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Return media notice HTML.
	 *
	 * @return string
	 */
	private function get_media_notice_html() {
		return sprintf(
			/* translators: %s is a TinyPNG hyperlink. */
			__(
				'Use PNG only for transparent images. For regular photos, use .jpeg, .jpg, or .webp. Before uploading images, optimize them with %s and keep every image under 0.5 MB / 500 KB.',
				self::TEXT_DOMAIN
			),
			$this->get_tinypng_link_html()
		);
	}

	/**
	 * Return TinyPNG hyperlink HTML.
	 *
	 * @return string
	 */
	private function get_tinypng_link_html() {
		return sprintf(
			'<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
			esc_url( 'https://tinypng.com' ),
			esc_html__( 'tinypng.com', self::TEXT_DOMAIN )
		);
	}

	/**
	 * Return allowed HTML tags for notice content.
	 *
	 * @return array<string, array<string, bool>>
	 */
	private function get_allowed_notice_html() {
		return array(
			'a'      => array(
				'href'   => true,
				'target' => true,
				'rel'    => true,
			),
			'strong' => array(),
		);
	}

	/**
	 * Return cache-busting version for plugin asset.
	 *
	 * @param string $relative_path Asset path relative to plugin root.
	 * @return string
	 */
	private function get_asset_version( $relative_path ) {
		$asset_path = dirname( __DIR__ ) . '/' . ltrim( (string) $relative_path, '/' );

		if ( file_exists( $asset_path ) ) {
			return (string) filemtime( $asset_path );
		}

		return self::VERSION;
	}

	/**
	 * Check whether current screen is main dashboard.
	 *
	 * @return bool
	 */
	private function is_dashboard_screen() {
		$screen = get_current_screen();

		return $screen && 'dashboard' === $screen->base;
	}

	/**
	 * Check whether current screen belongs to media library flow.
	 *
	 * @return bool
	 */
	private function is_media_screen() {
		$screen = get_current_screen();

		if ( ! $screen ) {
			return false;
		}

		return in_array( $screen->base, array( 'upload', 'media' ), true );
	}
}

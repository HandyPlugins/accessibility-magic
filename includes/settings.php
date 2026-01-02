<?php
/**
 * Settings Page
 *
 * @package AccessibilityMagic
 */

namespace AccessibilityMagic\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


use function AccessibilityMagic\Utils\get_doc_url;
use function AccessibilityMagic\Utils\get_supported_units;
use function AccessibilityMagic\Utils\mask_string;
use const AccessibilityMagic\Constants\BLOG_URL;
use const AccessibilityMagic\Constants\DEFAULT_SETTINGS;
use const AccessibilityMagic\Constants\DOCS_URL;
use const AccessibilityMagic\Constants\FAQ_URL;
use const AccessibilityMagic\Constants\GITHUB_URL;
use const AccessibilityMagic\Constants\SETTING_OPTION;
use const AccessibilityMagic\Constants\SUPPORT_URL;
use const AccessibilityMagic\Constants\TWITTER_URL;

// phpcs:disable WordPress.WhiteSpace.PrecisionAlignment.Found
// phpcs:disable Generic.WhiteSpace.DisallowSpaceIndent.SpacesUsed
// phpcs:disable WordPress.WP.I18n.MissingTranslatorsComment

/**
 * Default setup routine
 *
 * @return void
 */
function setup() {
	if ( ACCESSIBILITY_MAGIC_IS_NETWORK ) {
		add_action( 'network_admin_menu', __NAMESPACE__ . '\\admin_menu' );
		add_filter( 'network_admin_plugin_action_links_' . plugin_basename( ACCESSIBILITY_MAGIC_PLUGIN_FILE ), __NAMESPACE__ . '\\plugin_action_links' );
	} else {
		add_action( 'admin_menu', __NAMESPACE__ . '\\admin_menu' );
		add_filter( 'plugin_action_links_' . plugin_basename( ACCESSIBILITY_MAGIC_PLUGIN_FILE ), __NAMESPACE__ . '\\plugin_action_links' );
	}

	add_action( 'admin_init', __NAMESPACE__ . '\\save_settings' );
	add_filter( 'admin_body_class', __NAMESPACE__ . '\\add_sui_admin_body_class' );
}

/**
 * Add settings link to plugin actions in plugin list
 *
 * @param array $links Plugin action links.
 *
 * @return array Modified plugin action links.
 */
function plugin_action_links( $links ) {
	$settings_url = ACCESSIBILITY_MAGIC_IS_NETWORK
		? network_admin_url( 'settings.php?page=accessibility-toolkit' )
		: admin_url( 'options-general.php?page=accessibility-toolkit' );

	$settings_link = sprintf(
		'<a href="%s">%s</a>',
		esc_url( $settings_url ),
		esc_html__( 'Settings', 'accessibility-magic' )
	);

	array_unshift( $links, $settings_link );

	return $links;
}

/**
 * Add required class for shared UI
 *
 * @param string $classes css classes for admin area
 *
 * @return string
 * @see https://wpmudev.github.io/shared-ui/installation/
 */
function add_sui_admin_body_class( $classes ) {
	$classes .= ' sui-2-12-25 ';

	return $classes;
}


/**
 * Add menu item
 */
function admin_menu() {
	$parent = ACCESSIBILITY_MAGIC_IS_NETWORK ? 'settings.php' : 'options-general.php';

	add_submenu_page(
		$parent,
		esc_html__( 'Accessibility Magic', 'accessibility-magic' ),
		esc_html__( 'Accessibility Magic', 'accessibility-magic' ),
		apply_filters( 'accessibility_toolkit_admin_menu_cap', 'manage_options' ),
		'accessibility-magic',
		__NAMESPACE__ . '\settings_page'
	);
}

/**
 * Settings page
 */
function settings_page() {
	$settings = \AccessibilityMagic\Utils\get_settings();
	?>
	<?php if ( is_network_admin() ) : ?>
		<?php settings_errors(); ?>
	<?php endif; ?>

	<main class="sui-wrap">
		<div class="sui-header">
			<h1 class="sui-header-title">
				<?php esc_html_e( 'Accessibility Magic', 'accessibility-magic' ); ?>
			</h1>

			<!-- Float element to Right -->
			<div class="sui-actions-right">
				<a href="<?php echo esc_url( DOCS_URL ); ?>" class="sui-button sui-button-blue" target="_blank">
					<i class="sui-icon-academy" aria-hidden="true"></i>
					<?php esc_html_e( 'Documentation', 'accessibility-magic' ); ?>
				</a>
			</div>
		</div>

		<form method="post" action="">
			<?php wp_nonce_field( 'accessibility_magic_settings', 'accessibility_magic_settings' ); ?>
			<section class="sui-row-with-sidenav">

				<!-- TAB: Regular -->
				<div class="sui-box" data-tab="basic-options">

					<div class="sui-box-header">
						<h2 class="sui-box-title">
							<?php esc_html_e( 'Settings', 'accessibility-magic' ); ?>
						</h2>
						<div class="sui-actions-right sui-hidden-important" style="display: none;">
							<button type="submit" class="sui-button sui-button-blue" id="accessibility-toolkit-save-settings-top" data-msg="">
								<i class="sui-icon-save" aria-hidden="true"></i>
								<?php esc_html_e( 'Update settings', 'accessibility-magic' ); ?>
							</button>
						</div>
					</div>

					<div class="sui-box-body sui-upsell-items">
						<!-- Text Size Control -->
						<div class="sui-box-settings-row">
							<div class="sui-box-settings-col-1">
								<span class="sui-settings-label">
									<?php esc_html_e( 'Text Size Control', 'accessibility-magic' ); ?>
								</span>
							</div>

							<div class="sui-box-settings-col-2">
								<div class="sui-form-field">
									<label for="enable_text_size" class="sui-toggle">
										<input type="checkbox"
											   value="1"
											   name="enable_text_size"
											   id="enable_text_size"
											   aria-labelledby="enable_text_size_label"
											<?php checked( 1, $settings['enable_text_size'] ); ?>
										>
										<span class="sui-toggle-slider" aria-hidden="true"></span>
										<span id="enable_text_size_label" class="sui-toggle-label">
											<?php esc_html_e( 'Enable text size control. Allows to increase or decrease font size for better readability.', 'accessibility-magic' ); ?>
										</span>
									</label>
								</div>
							</div>
						</div>

						<!-- Text Space Control -->
						<div class="sui-box-settings-row">
							<div class="sui-box-settings-col-1">
								<span class="sui-settings-label">
									<?php esc_html_e( 'Text Space Control', 'accessibility-magic' ); ?>
								</span>
							</div>

							<div class="sui-box-settings-col-2">
								<div class="sui-form-field">
									<label for="enable_text_spacing" class="sui-toggle">
										<input type="checkbox"
											   value="1"
											   name="enable_text_spacing"
											   id="enable_text_spacing"
											   aria-labelledby="enable_text_spacing_label"
											<?php checked( 1, $settings['enable_text_spacing'] ); ?>
										>
										<span class="sui-toggle-slider" aria-hidden="true"></span>
										<span id="enable_text_spacing_label" class="sui-toggle-label">
											<?php esc_html_e( 'Enable text space control. Allows to expand or contract space between characters and words.', 'accessibility-magic' ); ?>
										</span>
									</label>
								</div>
							</div>
						</div>

						<!-- Line Height Control -->
						<div class="sui-box-settings-row">
							<div class="sui-box-settings-col-1">
								<span class="sui-settings-label">
									<?php esc_html_e( 'Line Height Control', 'accessibility-magic' ); ?>
								</span>
							</div>

							<div class="sui-box-settings-col-2">
								<div class="sui-form-field">
									<label for="enable_line_height" class="sui-toggle">
										<input type="checkbox"
											   value="1"
											   name="enable_line_height"
											   id="enable_line_height"
											   aria-labelledby="enable_line_height_label"
											<?php checked( 1, $settings['enable_line_height'] ); ?>
										>
										<span class="sui-toggle-slider" aria-hidden="true"></span>
										<span id="enable_line_height_label" class="sui-toggle-label">
											<?php esc_html_e( 'Enable line height control. Allows to adjust the vertical space between lines of text.', 'accessibility-magic' ); ?>
										</span>
									</label>
								</div>
							</div>
						</div>

						<!-- Line Height Control -->
						<div class="sui-box-settings-row sui-disabled">
							<div class="sui-box-settings-col-1">
								<span class="sui-settings-label">
									<?php esc_html_e( 'Invert Colors', 'accessibility-magic' ); ?>
									<span class="sui-tag sui-tag-pro"><?php esc_html_e( 'Pro', 'accessibility-magic' ); ?></span>
								</span>
							</div>

							<div class="sui-box-settings-col-2">
								<div class="sui-form-field">
									<label for="enable_invert_colors" class="sui-toggle">
										<input type="checkbox"
											   value="1"
											   name="enable_invert_colors"
											   id="enable_invert_colors"
											   aria-labelledby="enable_invert_colors_label"
											<?php checked( 1, $settings['enable_invert_colors'] ); ?>
										>
										<span class="sui-toggle-slider" aria-hidden="true"></span>
										<span id="enable_invert_colors_label" class="sui-toggle-label">
											<?php esc_html_e( 'Enable invert colors. Allows to switch the document\'s background between black and white, while correspondingly inverting the default text color from black to white and vice versa.', 'accessibility-magic' ); ?>
										</span>
									</label>
								</div>
							</div>
						</div>

						<!-- Gray Hues Control -->
						<div class="sui-box-settings-row">
							<div class="sui-box-settings-col-1">
								<span class="sui-settings-label">
									<?php esc_html_e( 'Gray Hues', 'accessibility-magic' ); ?>
								</span>
							</div>

							<div class="sui-box-settings-col-2">
								<div class="sui-form-field">
									<label for="enable_gray_hues" class="sui-toggle">
										<input type="checkbox"
											   value="1"
											   name="enable_gray_hues"
											   id="enable_gray_hues"
											   aria-labelledby="enable_gray_hues_label"
											<?php checked( 1, $settings['enable_gray_hues'] ); ?>
										>
										<span class="sui-toggle-slider" aria-hidden="true"></span>
										<span id="enable_gray_hues_label" class="sui-toggle-label">
											<?php esc_html_e( 'Enable gray hues. Allows to toggle the website\'s color scheme to grayscale, encompassing all content including images.', 'accessibility-magic' ); ?>
										</span>
									</label>
								</div>
							</div>
						</div>

						<!-- Underline Links Control -->
						<div class="sui-box-settings-row">
							<div class="sui-box-settings-col-1">
								<span class="sui-settings-label">
									<?php esc_html_e( 'Underline Links', 'accessibility-magic' ); ?>
								</span>
							</div>

							<div class="sui-box-settings-col-2">
								<div class="sui-form-field">
									<label for="enable_underline_links" class="sui-toggle">
										<input type="checkbox"
											   value="1"
											   name="enable_underline_links"
											   id="enable_underline_links"
											   aria-labelledby="enable_underline_links_label"
											<?php checked( 1, $settings['enable_underline_links'] ); ?>
										>
										<span class="sui-toggle-slider" aria-hidden="true"></span>
										<span id="enable_underline_links_label" class="sui-toggle-label">
											<?php esc_html_e( 'Enable underline links. Allows to underline all links within the document.', 'accessibility-magic' ); ?>
										</span>
									</label>
								</div>
							</div>
						</div>

						<!-- big cursor -->
						<div class="sui-box-settings-row">
							<div class="sui-box-settings-col-1">
								<span class="sui-settings-label">
									<?php esc_html_e( 'Big Cursor', 'accessibility-magic' ); ?>
								</span>
							</div>

							<div class="sui-box-settings-col-2">
								<div class="sui-form-field">
									<label for="enable_big_cursor" class="sui-toggle">
										<input type="checkbox"
											   value="1"
											   name="enable_big_cursor"
											   id="enable_big_cursor"
											   aria-labelledby="enable_big_cursor_label"
											<?php checked( 1, $settings['enable_big_cursor'] ); ?>
										>
										<span class="sui-toggle-slider" aria-hidden="true"></span>
										<span id="enable_big_cursor_label" class="sui-toggle-label">
											<?php esc_html_e( 'Enable big cursor. Replace the default mouse cursor with a larger version.', 'accessibility-magic' ); ?>
										</span>
									</label>
								</div>
							</div>
						</div>

						<!-- reading guide -->
						<div class="sui-box-settings-row">
							<div class="sui-box-settings-col-1">
								<span class="sui-settings-label">
									<?php esc_html_e( 'Reading Guide', 'accessibility-magic' ); ?>
								</span>
							</div>

							<div class="sui-box-settings-col-2">
								<div class="sui-form-field">
									<label for="enable_reading_guide" class="sui-toggle">
										<input type="checkbox"
											   value="1"
											   name="enable_reading_guide"
											   id="enable_reading_guide"
											   aria-labelledby="enable_reading_guide_label"
											<?php checked( 1, $settings['enable_reading_guide'] ); ?>
										>
										<span class="sui-toggle-slider" aria-hidden="true"></span>
										<span id="enable_reading_guide_label" class="sui-toggle-label">
											<?php esc_html_e( 'Enable reading guide. Display a guide line to assist in tracking text while reading.', 'accessibility-magic' ); ?>
										</span>
									</label>
								</div>
							</div>
						</div>

						<!-- Text-to-Speech -->
						<div class="sui-box-settings-row sui-disabled">
							<div class="sui-box-settings-col-1">
								<span class="sui-settings-label">
									<?php esc_html_e( 'Text to Speech', 'accessibility-magic' ); ?>
									<span class="sui-tag sui-tag-pro"><?php esc_html_e( 'Pro', 'accessibility-magic' ); ?></span>
								</span>
							</div>

							<div class="sui-box-settings-col-2">
								<div class="sui-form-field">
									<label for="enable_text_to_speech" class="sui-toggle">
										<input type="checkbox"
											   value="1"
											   name="enable_text_to_speech"
											   id="enable_text_to_speech"
											   aria-labelledby="enable_text_to_speech_label"
											<?php checked( 1, $settings['enable_text_to_speech'] ); ?>
										>
										<span class="sui-toggle-slider" aria-hidden="true"></span>
										<span id="enable_text_to_speech_label" class="sui-toggle-label">
											<?php esc_html_e( 'Enable the browser\'s text-to-speech function to read out text.', 'accessibility-magic' ); ?>
										</span>
									</label>
								</div>
							</div>
						</div>

						<!-- Disable Animations -->
						<div class="sui-box-settings-row sui-disabled">
							<div class="sui-box-settings-col-1">
								<span class="sui-settings-label">
									<?php esc_html_e( 'Animations', 'accessibility-magic' ); ?>
									<span class="sui-tag sui-tag-pro"><?php esc_html_e( 'Pro', 'accessibility-magic' ); ?></span>
								</span>
							</div>

							<div class="sui-box-settings-col-2">
								<div class="sui-form-field">
									<label for="disable_animations" class="sui-toggle">
										<input type="checkbox"
											   value="1"
											   name="disable_animations"
											   id="disable_animations"
											   aria-labelledby="disable_animations_label"
											<?php checked( 1, $settings['disable_animations'] ); ?>
										>
										<span class="sui-toggle-slider" aria-hidden="true"></span>
										<span id="disable_animations_label" class="sui-toggle-label">
											<?php esc_html_e( 'Add an option to disable animations.', 'accessibility-magic' ); ?>
										</span>
									</label>
								</div>
							</div>
						</div>

						<!-- hotkeys -->
						<div class="sui-box-settings-row">
							<div class="sui-box-settings-col-1">
								<span class="sui-settings-label">
									<?php esc_html_e( 'Hotkeys', 'accessibility-magic' ); ?>
								</span>
							</div>

							<div class="sui-box-settings-col-2">
								<div class="sui-form-field">
									<label for="enable_hotkeys" class="sui-toggle">
										<input type="checkbox"
											   value="1"
											   name="enable_hotkeys"
											   id="enable_hotkeys"
											   aria-labelledby="enable_hotkeys_label"
											<?php checked( 1, $settings['enable_hotkeys'] ); ?>
										>
										<span class="sui-toggle-slider" aria-hidden="true"></span>
										<span id="enable_hotkeys_label" class="sui-toggle-label">
											<?php esc_html_e( 'Use keyboard shortcuts to activate/deactivate features.', 'accessibility-magic' ); ?>
										</span>
										<span class="sui-description">

											<?php
                                            echo wp_kses_post(
                                                sprintf(
													/* translators: %s: list of hotkeys */
                                                    __( 'You can find a list of key mappings in the %s.', 'accessibility-magic' ),
                                                    '<a href="' . esc_url( get_doc_url( '/hotkeys/' ) ) . '" target="_blank">' . esc_html__( 'documentation', 'accessibility-magic' ) . '</a>'
                                                )
											);
                                            ?>

										</span>
									</label>
								</div>
							</div>
						</div>

						<!-- Accessibility Statement -->
						<div class="sui-box-settings-row">
							<div class="sui-box-settings-col-1">
								<span class="sui-settings-label">
									<?php esc_html_e( 'Accessibility Statement', 'accessibility-magic' ); ?>
								</span>
							</div>

							<div class="sui-box-settings-col-2">
								<div class="sui-form-field">
									<label for="accessibility_statement">

										<input name="accessibility_statement"
											   id="accessibility_statement"
											   class="sui-form-control"
											   aria-labelledby="email_subject_label"
											   type="text"
											   value="<?php echo esc_url( $settings['accessibility_statement'] ); ?>"
										>

										<span class="sui-toggle-slider" aria-hidden="true"></span>
										<span id="accessibility_statement_label" class="sui-toggle-label">
											<?php esc_html_e( 'Enter the accessibility statement URL. It opens in the iframe modal.', 'accessibility-magic' ); ?>
										</span>
									</label>
								</div>

								<div class="sui-form-field">
									<label for="as_iframe" class="sui-toggle">
										<input type="checkbox"
											   value="1"
											   name="as_iframe"
											   id="as_iframe"
											   aria-labelledby="as_iframe_label"
											<?php checked( 1, $settings['as_iframe'] ); ?>
										>
										<span class="sui-toggle-slider" aria-hidden="true"></span>
										<span id="as_iframe_label" class="sui-toggle-label">
											<?php esc_html_e( 'Open accessibility statment in iframe.', 'accessibility-magic' ); ?>
										</span>
									</label>
								</div>
							</div>
						</div>

						<!-- Exclusions -->
						<div class="sui-box-settings-row">
							<div class="sui-box-settings-col-1">
								<span class="sui-settings-label">
									<?php esc_html_e( 'Exclusions', 'accessibility-magic' ); ?>
								</span>
							</div>

							<div class="sui-box-settings-col-2">
								<div class="sui-row">
									<div class="sui-col-md-8">
										<div class="sui-form-field">
											<label for="excluded_pages" class="sui-label">
												<?php esc_html_e( 'Do not show the accessibility toolkit on these pages', 'accessibility-magic' ); ?>
											</label>
											<textarea
												placeholder="/example-page/"
												id="excluded_pages"
												name="excluded_pages"
												class="sui-form-control"
												aria-describedby="excluded_pages_description"
												rows="5"
											><?php echo esc_textarea( $settings['excluded_pages'] ); ?></textarea>
											<span id="excluded_pages_description" class="sui-description">
												<?php esc_html_e( 'The accessibility toolkit will not be added to these pages. It supports partial matching. (One per line)', 'accessibility-magic' ); ?>
												<a href="<?php echo esc_url( get_doc_url( '/excluded-pages/' ) ); ?>" target="_blank">(?)</a>
											</span>
										</div>
									</div>
								</div>
							</div>
						</div>

						<!-- Icon -->
						<div class="sui-box-settings-row sui-disabled">
							<div class="sui-box-settings-col-1">
								<span class="sui-settings-label">
									<?php esc_html_e( 'Icon', 'accessibility-magic' ); ?>
									<span class="sui-tag sui-tag-pro"><?php esc_html_e( 'Pro', 'accessibility-magic' ); ?></span>
								</span>

								<span class="sui-description">
									<?php esc_html_e( 'Customize the icon appearance and position.', 'accessibility-magic' ); ?>
									<a href="<?php echo esc_url( get_doc_url( '/customization/' ) ); ?>" target="_blank">(?)</a>
								</span>
							</div>

							<div class="sui-box-settings-col-2">
								<div class="sui-form-field">
									<label for="icon_circular" class="sui-toggle">
										<input type="checkbox"
											   value="1"
											   name="icon[circular]"
											   id="icon_circular"
											   aria-labelledby="icon_circular_label"
											<?php checked( 1, $settings['icon']['circular'] ); ?>
										>
										<span class="sui-toggle-slider" aria-hidden="true"></span>
										<span id="icon_circular_label" class="sui-toggle-label">
											<?php esc_html_e( 'Use circular icon.', 'accessibility-magic' ); ?>
										</span>
									</label>
								</div>

								<div class="sui-form-field">
									<fieldset id="iw_alignment">
										<span class="sui-description">
											<?php esc_html_e( 'Select icon position.', 'accessibility-magic' ); ?>
										</span>
										<table id="icon_position">
											<tbody>
											<tr>
												<td aria-label="<?php esc_attr_e( 'Top left', 'accessibility-magic' ); ?>">
													<label for="icon-position-top-left">
														<span class="screen-reader-text"><?php esc_html_e( 'Top left', 'accessibility-magic' ); ?></span>
														<input name="icon[position]" id="icon-position-top-left" type="radio" value="top-left" <?php checked( 'top-left', $settings['icon']['position'] ); ?>>
													</label>
												</td>
												<td aria-label="<?php esc_attr_e( 'Top right', 'accessibility-magic' ); ?>">
													<label for="icon-position-top-right">
														<span class="screen-reader-text"><?php esc_html_e( 'Top right', 'accessibility-magic' ); ?></span>
														<input name="icon[position]" id="icon-position-top-right" type="radio" value="top-right" <?php checked( 'top-right', $settings['icon']['position'] ); ?>>
													</label>
												</td>
											</tr>
											<tr>
												<td aria-label="<?php esc_attr_e( 'Bottom left', 'accessibility-magic' ); ?>">
													<label for="icon-position-bottom-left">
														<span class="screen-reader-text"><?php esc_html_e( 'Bottom left', 'accessibility-magic' ); ?></span>
														<input name="icon[position]" id="icon-position-bottom-left" type="radio" value="bottom-left" <?php checked( 'bottom-left', $settings['icon']['position'] ); ?>>
													</label>
												</td>
												<td aria-label="<?php esc_attr_e( 'Bottom right', 'accessibility-magic' ); ?>">
													<label for="icon-position-bottom-right">
														<span class="screen-reader-text"><?php esc_html_e( 'Bottom right', 'accessibility-magic' ); ?></span>
														<input name="icon[position]" id="icon-position-bottom-right" type="radio" value="bottom-right" <?php checked( 'bottom-right', $settings['icon']['position'] ); ?>>
													</label>
												</td>
											</tr>
											</tbody>
										</table>
									</fieldset>
								</div>

								<div class="sui-form-field-row">
									<label for="background_color" class="sui-description" id="background_color-label">
										<?php esc_html_e( 'Icon Background Color:', 'accessibility-magic' ); ?>
									</label>

									<input id="background_color" name="icon[background_color]" type="text" value="<?php echo esc_attr( $settings['icon']['background_color'] ); ?>" class="accessibility-toolkit-color-field" data-default-color="#4054b2" />
								</div>

								<div class="sui-form-field-row">

									<label for="icon_color" class="sui-description" id="background_color-label">
										<?php esc_html_e( 'Icon Color:', 'accessibility-magic' ); ?>
									</label>

									<input id="icon_color" name="icon[color]" type="text" value="<?php echo esc_attr( $settings['icon']['color'] ); ?>" class="accessibility-toolkit-color-field" data-default-color="#fff" />
								</div>

								<div class="sui-border-frame">
									<div class="sui-form-field">
										<span class="sui-description">
											<?php esc_html_e( 'Dimension', 'accessibility-magic' ); ?>
										</span>

										<div class="sui-form-field-row">
											<label for="dimension_w" id="dimension_w-label">
												<?php esc_html_e( 'Width:', 'accessibility-magic' ); ?>
												<input name="icon[dimension_w]" id="dimension_w" value="<?php echo esc_attr( $settings['icon']['dimension_w'] ); ?>" class="sui-form-control-sm" style="max-width: 80px;" type="number">
											</label>

											<label for="dimension_h" id="dimension_h-label">
												<?php esc_html_e( 'Height:', 'accessibility-magic' ); ?>
												<input name="icon[dimension_h]" value="<?php echo esc_attr( $settings['icon']['dimension_h'] ); ?>" id="dimension_h" class="sui-form-control-sm" style="max-width: 80px;" type="number">
											</label>

											<label for="dimension-unit-selector">
												<?php esc_html_e( 'Unit', 'accessibility-magic' ); ?>

												<?php $units = get_supported_units(); ?>
												<select name="icon[dimension_unit]" class="sui-select sui-select-inline" id="dimension-unit-selector">
													<?php foreach ( $units as $unit ) : ?>
														<option <?php selected( $unit, $settings['icon']['dimension_unit'] ); ?> value="<?php echo esc_attr( $unit ); ?>"><?php echo esc_html( $unit ); ?></option>
													<?php endforeach; ?>
												</select>
											</label>
										</div>
									</div>
								</div>


								<div class="sui-border-frame">
									<div class="sui-form-field">
										<span class="sui-description">
											<?php esc_html_e( 'Offset', 'accessibility-magic' ); ?>
										</span>

										<div class="sui-form-field-row">
											<label for="offset_x" id="offset_x-label">
												<?php esc_html_e( 'X (Horizontal Offset):', 'accessibility-magic' ); ?>
												<input name="icon[offset_x]" id="offset_x" value="<?php echo esc_attr( $settings['icon']['offset_x'] ); ?>" class="sui-form-control-sm" style="max-width: 80px;" type="number">
											</label>

											<label for="offset_y" id="offset_y-label">
												<?php esc_html_e( 'Y (Vertical Offset):', 'accessibility-magic' ); ?>
												<input name="icon[offset_y]" value="<?php echo esc_attr( $settings['icon']['offset_y'] ); ?>" id="offset_y" class="sui-form-control-sm" style="max-width: 80px;" type="number">
											</label>

											<label for="offset-unit-selector">
												<?php esc_html_e( 'Unit', 'accessibility-magic' ); ?>

												<?php $units = get_supported_units(); ?>
												<select name="icon[offset_unit]" class="sui-select sui-select-inline" id="offset-unit-selector">
													<?php foreach ( $units as $unit ) : ?>
														<option <?php selected( $unit, $settings['icon']['offset_unit'] ); ?> value="<?php echo esc_attr( $unit ); ?>"><?php echo esc_html( $unit ); ?></option>
													<?php endforeach; ?>
												</select>
											</label>
										</div>
									</div>
								</div>

							</div>
						</div>

						<div class="sui-box-settings-row">
							<div class="sui-upsell-row">
								<div class="sui-upsell-notice">
									<p><?php esc_html_e( 'Upgrade to Pro version to unlock advanced icon styling and advanced options.', 'accessibility-magic' ); ?><br>
										<a href="https://handyplugins.co/docs/accessibility-toolkit-customization/" rel="noopener" target="_blank" class="sui-button sui-button-purple" style="margin-top: 10px;color:#fff;"><?php esc_html_e( 'Try Pro today', 'accessibility-magic' ); ?></a>
									</p>
								</div>
							</div>
						</div>

					</div>

					<div class="sui-box-footer">
						<div class="sui-actions-left">
							<button type="submit" class="sui-button sui-button-blue" id="accessibility-toolkit-save-settings" data-msg="">
								<i class="sui-icon-save" aria-hidden="true"></i>
								<?php esc_html_e( 'Update settings', 'accessibility-magic' ); ?>
							</button>
						</div>
					</div>

				</div>
			</section>

		</form>

		<!-- ELEMENT: The Brand -->
		<div class="sui-footer">
			<?php
			echo wp_kses_post(
				sprintf(
				/* translators: %s: HandyPlugins URL */
					__( 'Made with <i class="sui-icon-heart"></i> by <a href="%s" rel="noopener" target="_blank">HandyPlugins</a>', 'accessibility-magic' ),
					'https://handyplugins.co/'
				)
			);
			?>
		</div>

		<footer>
			<!-- ELEMENT: Navigation -->
			<ul class="sui-footer-nav">
				<li><a href="<?php echo esc_url_raw( FAQ_URL ); ?>" target="_blank"><?php esc_html_e( 'FAQ', 'accessibility-magic' ); ?></a></li>
				<li><a href="<?php echo esc_url( BLOG_URL ); ?>" target="_blank"><?php esc_html_e( 'Blog', 'accessibility-magic' ); ?></a></li>
				<li><a href="<?php echo esc_url( SUPPORT_URL ); ?>" target="_blank"><?php esc_html_e( 'Support', 'accessibility-magic' ); ?></a></li>
				<li><a href="<?php echo esc_url( DOCS_URL ); ?>" target="_blank"><?php esc_html_e( 'Docs', 'accessibility-magic' ); ?></a></li>
			</ul>

			<!-- ELEMENT: Social Media -->
			<ul class="sui-footer-social">
				<li><a href="<?php echo esc_url( GITHUB_URL ); ?>" target="_blank" aria-label="<?php esc_attr_e( 'HandyPlugins GitHub URL', 'accessibility-magic' ); ?>">
						<i class="sui-icon-social-github" aria-hidden="true"></i>
						<span class="sui-screen-reader-text">GitHub</span>
					</a></li>
				<li><a href="<?php echo esc_url( TWITTER_URL ); ?>" target="_blank" aria-label="<?php esc_attr_e( 'HandyPlugins Twitter URL', 'accessibility-magic' ); ?>">
						<i class="sui-icon-social-twitter" aria-hidden="true"></i></a>
					<span class="sui-screen-reader-text">Twitter</span>
				</li>
			</ul>
		</footer>

	</main>

	<?php
}

/**
 * Save settings
 */
function save_settings() {

	if ( ! is_user_logged_in() ) {
		return;
	}

	$nonce = filter_input( INPUT_POST, 'accessibility_magic_settings', FILTER_SANITIZE_SPECIAL_CHARS );
	if ( wp_verify_nonce( $nonce, 'accessibility_magic_settings' ) ) {
		$settings                            = [];
		$settings['enable_text_size']        = boolval( filter_input( INPUT_POST, 'enable_text_size' ) );
		$settings['enable_text_spacing']     = boolval( filter_input( INPUT_POST, 'enable_text_spacing' ) );
		$settings['enable_line_height']      = boolval( filter_input( INPUT_POST, 'enable_line_height' ) );
		$settings['enable_gray_hues']        = boolval( filter_input( INPUT_POST, 'enable_gray_hues' ) );
		$settings['enable_underline_links']  = boolval( filter_input( INPUT_POST, 'enable_underline_links' ) );
		$settings['enable_big_cursor']       = boolval( filter_input( INPUT_POST, 'enable_big_cursor' ) );
		$settings['enable_reading_guide']    = boolval( filter_input( INPUT_POST, 'enable_reading_guide' ) );
		$settings['enable_hotkeys']          = boolval( filter_input( INPUT_POST, 'enable_hotkeys' ) );
		$settings['accessibility_statement'] = esc_url_raw( filter_input( INPUT_POST, 'accessibility_statement' ) );
		$settings['as_iframe']               = boolval( filter_input( INPUT_POST, 'as_iframe' ) );
		$settings['excluded_pages']          = sanitize_textarea_field( filter_input( INPUT_POST, 'excluded_pages' ) );

		if ( ACCESSIBILITY_MAGIC_IS_NETWORK ) {
			update_site_option( SETTING_OPTION, $settings );
		} else {
			update_option( SETTING_OPTION, $settings );
		}

		add_settings_error( SETTING_OPTION, 'accessibility-magic', esc_html__( 'Settings saved.', 'accessibility-magic' ), 'success' );
	}
}

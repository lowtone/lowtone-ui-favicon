<?php
/*
 * Plugin Name: UI: Favicon
 * Plugin URI: http://wordpress.lowtone.nl/plugins/ui-favicon/
 * Description: Select a favicon for your website.
 * Version: 1.0
 * Author: Lowtone <info@lowtone.nl>
 * Author URI: http://lowtone.nl
 * License: http://wordpress.lowtone.nl/license
 */
/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\plugins\lowtone\ui\favicon
 */

namespace lowtone\ui\favicon {

	use lowtone\ui\forms\Form,
		lowtone\ui\forms\Input,
		lowtone\content\packages\Package;

	// Includes
	
	if (!include_once WP_PLUGIN_DIR . "/lowtone-content/lowtone-content.php") 
		return trigger_error("Lowtone Content plugin is required", E_USER_ERROR) && false;

	// Init

	Package::init(array(
			Package::INIT_PACKAGES => array("lowtone"),
			Package::INIT_MERGED_PATH => __NAMESPACE__,
			Package::INIT_SUCCESS => function() {
				
				add_action("admin_init", function() {

					register_setting("favicon", "favicon");

				});

				add_action("admin_menu", function() {
					add_theme_page(__("Favicon", "lowtone_ui_favicon"), __("Favicon", "lowtone_ui_favicon"), "manage_options", "lowtone_ui_favicon", function() {
						echo '<div class="wrap">' . 
							get_screen_icon() . 
							'<h2>' . __("Favicon", "lowtone_ui_favicon") . '</h2>' . 
							'<form method="post" action="options.php">';

						settings_fields("favicon");

						do_settings_sections("lowtone_ui_favicon");

						submit_button();

						echo '</form>' .
							'</div>';
					});

					add_settings_section("lowtone_ui_favicon", __("Favicon", "lowtone_ui_favicon"), function() {
						echo '<p>' . __("Submit the URL to your icon.", "lowtone_ui_favicon") . '</p>';
					}, "lowtone_ui_favicon");

					$form = new Form();

					add_settings_field("favicon", __("Favicon URL", "lowtone_ui_favicon"), function() use ($form) {

						$form
							->createInput(Input::TYPE_TEXT, array(
								Input::PROPERTY_NAME => "favicon",
								Input::PROPERTY_VALUE =>  get_option("favicon")
							))
							->addClass("setting")
							->out();

					}, "lowtone_ui_favicon", "lowtone_ui_favicon");
				});

				add_action("wp_head", function() {
					if (!($favIcon = trim(get_option("favicon"))))
						return;

					echo '<link rel="shortcut icon" href="' . htmlentities($favIcon) . '" />';
				});

				// Register textdomain

				add_action("plugins_loaded", function() {
					load_plugin_textdomain("lowtone_ui_favicon", false, basename(__DIR__) . "/assets/languages");
				});

			}
		));

}
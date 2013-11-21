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

	use lowtone\types\arrays\Map,
		lowtone\ui\forms\Form,
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

				$settings = new Map(favicon());

				$icon = function($id) use ($settings) {
					return call_user_func_array(array($settings, "path"), func_get_args());
				};
				
				add_action("admin_init", function() {

					register_setting("favicon", "favicon");

				});

				add_action("admin_menu", function() use ($icon) {
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

					$form = new Form();

					// Default

					add_settings_section("lowtone_ui_favicon_default", __("Default", "lowtone_ui_favicon"), function() {
						echo '<p>' . __("Default icon.", "lowtone_ui_favicon") . '</p>';
					}, "lowtone_ui_favicon");

					add_settings_field("favicon_default", __("Default icon", "lowtone_ui_favicon"), function() use ($form, $icon) {

						$form
							->createInput(Input::TYPE_TEXT, array(
								Input::PROPERTY_NAME => array("favicon", "default"),
								Input::PROPERTY_VALUE =>  $icon("default"),
							))
							->addClass("setting")
							->out();

					}, "lowtone_ui_favicon", "lowtone_ui_favicon_default");

					// Touch

					add_settings_section("lowtone_ui_favicon_touch", __("Touch", "lowtone_ui_favicon"), function() {
						echo '<p>' . __("Touch icon for iOS 2.0+ and Android 2.1+.", "lowtone_ui_favicon") . '</p>';
					}, "lowtone_ui_favicon");

					add_settings_field("favicon_touch", __("Default icon", "lowtone_ui_favicon"), function() use ($form, $icon) {

						$form
							->createInput(Input::TYPE_TEXT, array(
								Input::PROPERTY_NAME => array("favicon", "touch"),
								Input::PROPERTY_VALUE =>  $icon("touch"),
							))
							->addClass("setting")
							->out();

					}, "lowtone_ui_favicon", "lowtone_ui_favicon_touch");

					// Tile

					add_settings_section("lowtone_ui_favicon_tile", __("Tile", "lowtone_ui_favicon"), function() {
						echo '<p>' . __("Windows 8 style tile icon.", "lowtone_ui_favicon") . '</p>';
					}, "lowtone_ui_favicon");

					add_settings_field("favicon_tile_icon", __("Tile icon", "lowtone_ui_favicon"), function() use ($form, $icon) {

						$form
							->createInput(Input::TYPE_TEXT, array(
								Input::PROPERTY_NAME => array("favicon", "tile", "icon"),
								Input::PROPERTY_VALUE =>  $icon(array("tile", "icon")),
							))
							->addClass("setting")
							->out();

					}, "lowtone_ui_favicon", "lowtone_ui_favicon_tile");

					add_settings_field("favicon_tile_color", __("Tile color", "lowtone_ui_favicon"), function() use ($form, $icon) {

						$form
							->createInput(Input::TYPE_TEXT, array(
								Input::PROPERTY_NAME => array("favicon", "tile", "color"),
								Input::PROPERTY_VALUE =>  $icon(array("tile", "color")),
							))
							->addClass("setting")
							->out();

					}, "lowtone_ui_favicon", "lowtone_ui_favicon_tile");

					add_settings_field("favicon_tile_news", __("Include news", "lowtone_ui_favicon"), function() use ($form, $icon) {

						$form
							->createInput(Input::TYPE_CHECKBOX, array(
								Input::PROPERTY_NAME => array("favicon", "tile", "include_news"),
								Input::PROPERTY_VALUE =>  1,
								Input::PROPERTY_SELECTED => $icon(array("tile", "include_news")),
							))
							->addClass("setting")
							->out();

					}, "lowtone_ui_favicon", "lowtone_ui_favicon_tile");
				});

				// Icon output

				add_action("wp_head", function() use ($settings, $icon) {
					if ($settings->count() < 1)
						return;

					if ($default = $icon("default")) 
						echo '<link rel="icon" sizes="16x16 32x32" href="' . ($escapedDefault = htmlentities($default)) . '" />' . 
							'<!--[if IE]><link rel="shortcut icon" href="' . $escapedDefault . '"><![endif]-->';

					if ($tile = $icon("tile")) {
						echo '<meta name="msapplication-TileColor" content="' . htmlentities($tile["color"]) . '">' .
							'<meta name="msapplication-TileImage" content="' . htmlentities($tile["icon"]) . '">';

						if (isset($tile["include_news"]))
							echo '<!-- Tile news -->';

					}
					
				});

				// Register textdomain

				add_action("plugins_loaded", function() {
					load_plugin_textdomain("lowtone_ui_favicon", false, basename(__DIR__) . "/assets/languages");
				});

			}
		));

	// Functions
	
	function favicon() {
		return get_option("favicon") ?: array();
	}

}
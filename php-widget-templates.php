<?php
/**
 * @package PhpWidgetTemplates_Widget
 * @version 1.0
 */
/*
Plugin Name: PHP Widget Templates
Plugin URI: http://wordpress.org/extend/plugins/php-widget-templates/
Description: This plugin allows you to assign php files to widget slots.
Author: Philip Schalm
Version: 1.0
Author URI: http://github.com/pnomolos/
*/

	defined('ABSPATH') or die("Cannot access pages directly.");
	defined("DS") or define("DS", DIRECTORY_SEPARATOR);

	class PhpWidgetTemplates_Widget extends WP_Widget {
		function PhpWidgetTemplates_Widget() {
			// widget actual processes
			$this->WP_Widget( 
				$id = 'phpwidgettemplates_widget', 
				$name = 'Widget Template', 
				$options = array('description' => 'Execute a widget template') 
			);
		}

		function form($instance)
			{
				// outputs the options form on admin
				$directory = get_stylesheet_directory();
				$files = array();
				$template_file = (array_key_exists('template_file', $instance)) ? attribute_escape($instance['template_file']) : '';
				$title = (array_key_exists('title', $instance)) ? attribute_escape($instance['title']) : '';
				
				foreach (glob($directory.DS.'widget-*.php') as $filename) {
					$name = pathinfo($filename, PATHINFO_BASENAME);
					$files[$name] = $name;
				}
					
				?>
					<p>
						<label for="<?php echo $this->get_field_id('title') ?>">Title:</label><br>
						<input type="text" name="<?php echo $this->get_field_name('title') ?>" id="<?php echo $this->get_field_id('title') ?>" value="<?php echo $title ?>">
					</p>
					<p>
						<label for="<?php echo $this->get_field_id('template_file') ?>">Widget File:</label><br>
						<select name="<?php echo $this->get_field_name('template_file'); ?>" id="<?php echo $this->get_field_name('template_file') ?>">
							<option value="">-- Select a file --</option>
							<?php foreach ($files as $path => $name): $selected = ($path == $template_file ? ' selected="selected"' : '' ); ?>
								<option value="<?php echo htmlentities($path) ?>"<?php echo $selected ?>><?php echo htmlentities($name) ?></option>
							<?php endforeach; ?>
						</select>
					</p>
				<?php 
			}

			function update($new_instance, $old_instance)
			{
				// processes widget options to be saved
				$instance = wp_parse_args($new_instance, $old_instance);
				return $instance;
			}

			function widget($args, $instance)
			{
				// outputs the content of the widget
				extract($args);
				$title = apply_filters('widget_title', $instance['title']);
				$template_file = $instance['template_file'];
				
				echo $before_widget;
				if ($title)
					echo $before_title . $title . $after_title;
				
				if ($template_file && file_exists(get_stylesheet_directory().DS.$template_file))
					include get_stylesheet_directory().DS.$template_file;
				
				echo $after_widget;
			}

	}
	
	add_action( 'widgets_init', create_function( '', 'register_widget("PhpWidgetTemplates_Widget");' ) );
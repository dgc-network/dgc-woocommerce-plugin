<?php
/**
 * dgc Attribute Filter
 */
if (!class_exists('DGC_Attribute_Filter_Widget')) {
	class DGC_Attribute_Filter_Widget extends WP_Widget {
		/**
		 * Register widget with WordPress.
		 */
		function __construct() {
			parent::__construct(
				'dgc-attribute-filter', // Base ID
				__('dgc Attribute Filter', 'textdomain'), // Name
				array('description' => __('Filter woocommerce products by attribute.', 'textdomain')) // Args
			);
		}

		/**
		 * Frontend display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget($args, $instance) {
			if (!is_post_type_archive('product') && !is_tax(get_object_taxonomies('product'))) {
				return;
			}

			// enqueue necessary scripts
			wp_enqueue_style('dgc-style');
			wp_enqueue_style('font-awesome');
			wp_enqueue_script('dgc-script');
			
			if (empty($instance['attr_name']) && empty($instance['query_type'])) {
				return;
			}

			$enable_multiple = (!empty($instance['enable_multiple'])) ? (bool)$instance['enable_multiple'] : '';
			$show_count = (!empty($instance['show_count'])) ? (bool)$instance['show_count'] : '';
			$enable_hierarchy = (!empty($instance['hierarchical'])) ? (bool)$instance['hierarchical'] : '';
			$show_children_only = (!empty($instance['show_children_only'])) ? (bool)$instance['show_children_only'] : '';
			$open_by_default = (!empty($instance['open_by_default'])) ? (bool)$instance['open_by_default'] : '';
			$display_type = (!empty($instance['display_type'])) ? $instance['display_type'] : '';

			$attribute_name = $instance['attr_name'];
			$taxonomy   = 'pa_' . $attribute_name;
			$query_type = $instance['query_type'];
			$data_key   = ($query_type === 'and') ? 'attra-' . $attribute_name : 'attro-' . $attribute_name;

			// parse url
			$url = $_SERVER['QUERY_STRING'];
			parse_str($url, $url_array);

			$attr_args = array(
				'taxonomy'           => $taxonomy,
				'data_key'           => $data_key,
				'query_type'         => $query_type,
				'enable_multiple'    => $enable_multiple,
				'show_count'         => $show_count,
				'enable_hierarchy'   => $enable_hierarchy,
				'show_children_only' => $show_children_only,
				'open_by_default'    => $open_by_default,
				'url_array'          => $url_array
			);

			// if display type list
            switch ($display_type) {
                case 'list':
                    $output = dgc_list_terms($attr_args);
                    break;
                case 'dropdown':
                    $output = dgc_dropdown_terms($attr_args);
                    break;
                case 'slider':
                    $output = dgc_slider_terms($attr_args);
                    break;
            }

            $html = $output['html'];
			$found = $output['found'];

			// if display type list
			if (!empty($instance['display_type']) && $instance['display_type'] === 'list') {}

			extract($args);

			// Add class to before_widget from within a custom widget
			// http://wordpress.stackexchange.com/questions/18942/add-class-to-before-widget-from-within-a-custom-widget

            $widget_class = 'dgc-ajax-filter dgc-ajax-filter_' . $display_type;
			// if $selected_terms array is empty we will hide this widget totally
            if ($found === false) {
                $widget_class .= ' dgc-ajax-filter_hidden';
            }
            if (!empty($_GET[$data_key]) || $open_by_default) {
                $widget_class .= ' uk-open';
            }

			// no class found, so add it
			if (strpos($before_widget, 'class') === false) {
				$before_widget = str_replace('>', 'class="' . $widget_class . '"', $before_widget);
			}
			// class found but not the one that we need, so add it
			else {
				$before_widget = str_replace('class="', 'class="' . $widget_class . ' ', $before_widget);
			}

			echo $before_widget;

			if (!empty($instance['title'])) {
				echo $args['before_title'] . apply_filters('widget_title', $instance['title']). $args['after_title'];
			}

			echo $html;

			echo $args['after_widget'];
		}

		/**
		 * Backend widget form.
		 *
		 * @see WP_Widget::form()
		 *
		 * @param array $instance Previously saved values from database.
		 */
		public function form($instance) {
			?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php printf(__('Title:', 'textdomain')); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo (!empty($instance['title']) ? esc_attr($instance['title']) : ''); ?>">
			</p>
			<p>
			<?php
			$attribute_taxonomies = wc_get_attribute_taxonomies();
			if (sizeof($attribute_taxonomies) > 0) {
				?>
				<label for="<?php echo $this->get_field_id('attr_name'); ?>"><?php printf(__('Attribute', 'textdomain')); ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id('attr_name'); ?>" name="<?php echo $this->get_field_name('attr_name'); ?>">
					<?php
					foreach ($attribute_taxonomies as $taxonomy) {
						echo '<option value="' . $taxonomy->attribute_name . '" ' . ((!empty($instance['attr_name']) && $instance['attr_name'] === $taxonomy->attribute_name) ? 'selected="selected"' : '') . '>' . $taxonomy->attribute_label . '</option>';
					}
					?>
				</select>
				<?php
			} else {
				printf(__('No attribute found!', 'textdomain'));
			}
			?>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('display_type'); ?>"><?php printf(__('Display Type')) ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id('display_type'); ?>" name="<?php echo $this->get_field_name('display_type'); ?>">
					<option value="slider" <?php echo ((!empty($instance['display_type']) && $instance['display_type'] === 'slider') ? 'selected="selected"' : ''); ?>><?php printf(__('Slider', 'textdomain')); ?></option>
					<option value="list" <?php echo ((!empty($instance['display_type']) && $instance['display_type'] === 'list') ? 'selected="selected"' : ''); ?>><?php printf(__('List', 'textdomain')); ?></option>
					<option value="dropdown" <?php echo ((!empty($instance['display_type']) && $instance['display_type'] === 'dropdown') ? 'selected="selected"' : ''); ?>><?php printf(__('Dropdown', 'textdomain')); ?></option>
				</select>
			</p>
            <p>
                <label for="<?php echo $this->get_field_id('query_type'); ?>"><?php printf(__('Query Type')) ?></label>
                <select class="widefat" id="<?php echo $this->get_field_id('query_type'); ?>" name="<?php echo $this->get_field_name('query_type'); ?>">
                    <option value="and" <?php echo ((!empty($instance['query_type']) && $instance['query_type'] === 'and') ? 'selected="selected"' : ''); ?>><?php printf(__('AND', 'textdomain')); ?></option>
                    <option value="or" <?php echo ((!empty($instance['query_type']) && $instance['query_type'] === 'or') ? 'selected="selected"' : ''); ?>><?php printf(__('OR', 'textdomain')); ?></option>
                </select>
            </p>
            <div class="<?php echo (isset($instance['display_type']) && $instance['display_type'] === 'slider') ? 'hidden' : ''; ?>">
                <p>
                    <input id="<?php echo $this->get_field_id('enable_multiple'); ?>" name="<?php echo $this->get_field_name('enable_multiple'); ?>" type="checkbox" value="1" <?php echo (!empty($instance['enable_multiple']) && $instance['enable_multiple'] == true) ? 'checked="checked"' : ''; ?>>
                    <label for="<?php echo $this->get_field_id('enable_multiple'); ?>"><?php printf(__('Enable multiple filter', 'textdomain')); ?></label>
                </p>
                <p>
                    <input id="<?php echo $this->get_field_id('show_count'); ?>" name="<?php echo $this->get_field_name('show_count'); ?>" type="checkbox" value="1" <?php echo (!empty($instance['show_count']) && $instance['show_count'] == true) ? 'checked="checked"' : ''; ?>>
                    <label for="<?php echo $this->get_field_id('show_count'); ?>"><?php printf(__('Show count', 'textdomain')); ?></label>
                </p>
                <p>
                    <input id="<?php echo $this->get_field_id('hierarchical'); ?>" name="<?php echo $this->get_field_name('hierarchical'); ?>" type="checkbox" value="1" <?php echo (!empty($instance['hierarchical']) && $instance['hierarchical'] == true) ? 'checked="checked"' : ''; ?>>
                    <label for="<?php echo $this->get_field_id('hierarchical'); ?>"><?php printf(__('Show hierarchy', 'textdomain')); ?></label>
                </p>
                <p>
                    <input id="<?php echo $this->get_field_id('show_children_only'); ?>" name="<?php echo $this->get_field_name('show_children_only'); ?>" type="checkbox" value="1" <?php echo (!empty($instance['show_children_only']) && $instance['show_children_only'] == true) ? 'checked="checked"' : ''; ?>>
                    <label for="<?php echo $this->get_field_id('show_children_only'); ?>"><?php printf(__('Only show children of the current attribute', 'textdomain')); ?></label>
                </p>
                <p>
                    <input id="<?php echo $this->get_field_id('open_by_default'); ?>" name="<?php echo $this->get_field_name('open_by_default'); ?>" type="checkbox" value="1" <?php echo (!empty($instance['open_by_default']) && $instance['open_by_default'] == true) ? 'checked="checked"' : ''; ?>>
                    <label for="<?php echo $this->get_field_id('open_by_default'); ?>"><?php printf(__('Open By Default', 'textdomain')); ?></label>
                </p>
            </div>
			<?php
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update($new_instance, $old_instance) {
			$instance = array();
			$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
			$instance['attr_name'] = (!empty($new_instance['attr_name'])) ? strip_tags($new_instance['attr_name']) : '';
			$instance['display_type'] = (!empty($new_instance['display_type'])) ? strip_tags($new_instance['display_type']) : '';
			$instance['query_type'] = (!empty($new_instance['query_type'])) ? strip_tags($new_instance['query_type']) : '';
			$instance['enable_multiple'] = (!empty($new_instance['enable_multiple'])) ? strip_tags($new_instance['enable_multiple']) : '';
			$instance['show_count'] = (!empty($new_instance['show_count'])) ? strip_tags($new_instance['show_count']) : '';
			$instance['hierarchical'] = (!empty($new_instance['hierarchical'])) ? strip_tags($new_instance['hierarchical']) : '';
			$instance['show_children_only'] = (!empty($new_instance['show_children_only'])) ? strip_tags($new_instance['show_children_only']) : '';
			$instance['open_by_default'] = (!empty($new_instance['open_by_default'])) ? strip_tags($new_instance['open_by_default']) : '';
			return $instance;
		}
	}
}

// register widget
if (!function_exists('dgc_register_attribute_filter_widget')) {
	function dgc_register_attribute_filter_widget() {
		register_widget('DGC_Attribute_Filter_Widget');
	}
	add_action('widgets_init', 'dgc_register_attribute_filter_widget');
}
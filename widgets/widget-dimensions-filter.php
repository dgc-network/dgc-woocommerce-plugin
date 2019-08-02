<?php
/**
 * WC Ajax Product Filter by Dimensions
 */
if (!class_exists('DGC_Dimensions_Filter_Widget')) {
    class DGC_Dimensions_Filter_Widget extends WP_Widget {
        /**
         * Register widget with WordPress.
         */
        function __construct() {
            parent::__construct(
                'dgc-dimensions-filter', // Base ID
                __('WC Ajax Product Filter by Dimensions', 'textdomain'), // Name
                array('description' => __('Filter woocommerce products by dimensions.', 'textdomain')) // Args
            );
        }

        /**
         * Front-end display of widget.
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

            global $wcapf;

            // price range for filtered products
            $filtered_range = $dgc->getMetaRange($instance['attr_name'], true);

            // price range for all published products
            $unfiltered_range = $dgc->getMetaRange($instance['attr_name'], false);

            $html = '';

            // to be sure that these values are number
			$min_val = $max_val = $available_min_val = $available_max_val = null;

            if (sizeof($unfiltered_range) === 2) {
                $min_val = $unfiltered_range[0];
                $max_val = $unfiltered_range[1];
            }

			if (sizeof($filtered_range) === 2) {
				$min_val = $filtered_range[0];
				$max_val = $filtered_range[1];
			}

            // required scripts
            // enqueue necessary scripts
            wp_enqueue_style('dgc-style');
            wp_enqueue_style('font-awesome');
            wp_enqueue_script('dgc-script');
            wp_enqueue_script('dgc-ion-rangeslider-script');
            wp_enqueue_script('dgc-dimensions-filter-script');
            wp_enqueue_style('dgc-ion-rangeslider-base-style');
            wp_enqueue_style('dgc-ion-rangeslider-skin-style');

            // get values from url
            $set_min_val = null;
            if (isset($_GET['min-' . $instance['attr_name']]) && !empty($_GET['min-' . $instance['attr_name']])) {
                $set_min_val = (int)$_GET['min-' . $instance['attr_name']];
            }

            $set_max_val = null;
            if (isset($_GET['max-' . $instance['attr_name']]) && !empty($_GET['max-' . $instance['attr_name']])) {
                $set_max_val = (int)$_GET['max-' . $instance['attr_name']];
            }

            // HTML markup for dimensions slider
            $html .= '<div class="dgc-dimensions-filter-wrapper">';
            $attr = '';
            if ($min_val !== null) {
                $attr .= ' data-min="' . $min_val . '"';
            }
            if ($max_val !== null) {
                $attr .= ' data-max="' . $max_val . '"';
            }
            if ($set_min_val !== null) {
                $attr .= ' data-from="' . $set_min_val . '"';
            }
            if ($set_max_val !== null) {
                $attr .= ' data-to="' . $set_max_val . '"';
            }
            if ($available_min_val !== null) {
                    $attr .= ' data-from-min="' . $available_min_val . '"';
            }
            if ($available_max_val !== null) {
                    $attr .= ' data-to-max="' . $available_max_val . '"';
            }
            $html .= '<input class="dgc-dimensions-slider" ' . $attr . ' name="' . $instance['attr_name'] . '"/>';
            $html .= '</div>';

            extract($args);

            // Add class to before_widget from within a custom widget
            // http://wordpress.stackexchange.com/questions/18942/add-class-to-before-widget-from-within-a-custom-widget

            $widget_class = 'dgc-ajax-filter dgc-ajax-filter_dimensions dgc-ajax-filter_slider';

            if (!empty($_GET['min-' . $instance['attr_name']]) || !empty($_GET['max-' . $instance['attr_name']]) || $instance['open_by_default']) {
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
         * Back-end widget form.
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
                <label for="<?php echo $this->get_field_id('attr_name'); ?>"><?php printf(__('Attribute', 'textdomain')); ?></label>
                <select class="widefat" id="<?php echo $this->get_field_id('attr_name'); ?>" name="<?php echo $this->get_field_name('attr_name'); ?>">
                    <option value="_length" <?php echo ((!empty($instance['attr_name']) && $instance['attr_name'] === '_length') ? 'selected="selected"' : ''); ?>><?php printf(__('Length', 'textdomain')); ?></option>
                    <option value="_width" <?php echo ((!empty($instance['attr_name']) && $instance['attr_name'] === '_width') ? 'selected="selected"' : ''); ?>><?php printf(__('Width', 'textdomain')); ?></option>
                    <option value="_height" <?php echo ((!empty($instance['attr_name']) && $instance['attr_name'] === '_height') ? 'selected="selected"' : ''); ?>><?php printf(__('Height', 'textdomain')); ?></option>
                    <option value="_weight" <?php echo ((!empty($instance['attr_name']) && $instance['attr_name'] === '_weight') ? 'selected="selected"' : ''); ?>><?php printf(__('Weight', 'textdomain')); ?></option>
                </select>
            </p>
            <p>
                <input id="<?php echo $this->get_field_id('open_by_default'); ?>" name="<?php echo $this->get_field_name('open_by_default'); ?>" type="checkbox" value="1" <?php echo (!empty($instance['open_by_default']) && $instance['open_by_default'] == true) ? 'checked="checked"' : ''; ?>>
                <label for="<?php echo $this->get_field_id('open_by_default'); ?>"><?php printf(__('Open By Default', 'textdomain')); ?></label>
            </p>
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
            $instance['open_by_default'] = (!empty($new_instance['open_by_default'])) ? strip_tags($new_instance['open_by_default']) : '';
            return $instance;
        }
    }
}

// register widget
if (!function_exists('dgc_register_dimensions_filter_widget')) {
    function dgc_register_dimensions_filter_widget() {
        register_widget('DGC_Dimensions_Filter_Widget');
    }
    add_action('widgets_init', 'dgc_register_dimensions_filter_widget');
}

<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Elementor_Juicer_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'juicer_widget';
    }

    public function get_title() {
        return __('Juicer Social Wall', 'juicer');
    }

    public function get_icon() {
        return 'juicer-widget-icon';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function _register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'juicer'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT
            ]
        );

        $this->add_control(
            'shortcode',
            [
                'label' => __('Shortcode', 'juicer') . ' <span class="juicer-tooltip" title="Enter the shortcode for the Juicer feed. Example: [juicer name=&quot;your_feed_name&quot;]">&#x1F6C8;</span>',
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'placeholder' => __('Enter your shortcode here', 'juicer'),
                'default' => '[juicer name="your_feed_name"]',
            ]
        );

        $this->add_control(
            'per',
            [
                'label' => __('Posts Per Page', 'juicer') . ' <span class="juicer-tooltip" title="The maximum number of posts displayed at one time.">&#x1F6C8;</span>',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 100,
            ]
        );

        $this->add_control(
            'columns',
            [
                'label' => __('Columns', 'juicer') . ' <span class="juicer-tooltip" title="Specify the number of columns for the feed layout.">&#x1F6C8;</span>',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 3,
            ]
        );

        $this->add_control(
            'pages',
            [
                'label' => __('Total Pages', 'juicer') . ' <span class="juicer-tooltip" title="The maximum number of pages you would like to load. Set to 0 for no limit.">&#x1F6C8;</span>',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 0,
            ]
        );

        $this->add_control(
            'truncate',
            [
                'label' => __('Truncate Post Length', 'juicer') . ' <span class="juicer-tooltip" title="Truncate each post to this number of characters. Set to 0 for no truncation.">&#x1F6C8;</span>',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 0,
            ]
        );

        $this->add_control(
            'style',
            [
                'label' => __('Style', 'juicer') . ' <span class="juicer-tooltip" title="Choose a style for your feed display.">&#x1F6C8;</span>',
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $this->get_control_options('style'),
                'default' => 'modern',
            ]
        );

        $this->add_control(
            'filter',
            [
                'label' => __('Filter', 'juicer') . ' <span class="juicer-tooltip" title="Filter posts by social network, source account, or hashtag. Examples: Facebook, LinkedIn, #tbt. Separate multiple sources with commas.">&#x1F6C8;</span>',
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('Enter filter', 'juicer'),
            ]
        );

        $this->add_control(
            'spacing',
            [
                'label' => __('Spacing', 'juicer') . ' <span class="juicer-tooltip" title="Set the spacing between posts in the feed.">&#x1F6C8;</span>',
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('Enter spacing', 'juicer'),
            ]
        );

        // Add custom control for date range picker
        $this->add_control(
            'daterange',
            [
                'label' => __('Date Range', 'juicer') . ' <span class="juicer-tooltip" title="Specify a date range for the posts to display. Format: YYYY-MM-DD - YYYY-MM-DD.">&#x1F6C8;</span>',
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'default' => '',
                'data-setting' => 'daterange'
            ]
        );

        $this->add_control(
            'overlay',
            [
                'label' => __('Open Overlay on Click', 'juicer') . ' <span class="juicer-tooltip" title="Set to “true” to open an overlay when a post is clicked in your feed. If set to false, it will take you directly to the post on the social media provider.">&#x1F6C8;</span>',
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $this->get_control_options('overlay'),
                'default' => 'none',
            ]
        );

        $this->add_control(
            'after',
            [
                'label' => __('Run JS After Render', 'juicer') . ' <span class="juicer-tooltip" title="Specify the name of the JavaScript function to run after the feed has rendered.">&#x1F6C8;</span>',
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('Enter JS function name', 'juicer'),
                'default' => '',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        if (\Elementor\Plugin::instance()->editor->is_edit_mode()) {
            echo '<div class="juicer-preview-message">' . __('Set your options and preview the page to see the Juicer feed.', 'juicer') . '</div>';
        } else {
            // The shortcode itself is left as authored: it must stay a parseable shortcode
            // for do_shortcode(), and only users who can edit with Elementor can set it.
            $shortcode = $settings['shortcode'];
            $sanitized = $this->sanitize_settings($settings);

            foreach ($sanitized as $attribute => $value) {
                // Emptiness is tested against the original setting so that values PHP reads
                // as empty -- notably 0 -- keep being omitted exactly as they always were.
                // The sanitized value is checked too, so a setting whose entire content was
                // removed as unsafe does not leave an empty attribute behind.
                if (empty($settings[$attribute]) || $value === '') {
                    continue;
                }
                $shortcode = str_replace(']', ' ' . $attribute . '="' . $value . '"' . ']', $shortcode);
            }

            echo do_shortcode($shortcode);
        }
    }

    // Sanitizes each setting at the source, before it is concatenated into the shortcode
    // string. Values that cannot be made safe are dropped rather than coerced, so a
    // setting this plugin does not recognise is never silently rewritten into a different
    // one. Returns the attributes to emit, in the order they should appear.
    // Assignments are in the order the attributes are emitted, which is the order they were
    // emitted in before sanitizing was added. Keeping that implicit in the assignment order
    // avoids a separate list of attribute names that could fall out of step with this one.
    private function sanitize_settings($settings) {
        $sanitized = [];

        $sanitized['per'] = $this->sanitize_count($settings, 'per');
        $sanitized['pages'] = $this->sanitize_count($settings, 'pages');
        $sanitized['truncate'] = $this->sanitize_count($settings, 'truncate');
        $sanitized['style'] = $this->sanitize_option($settings, 'style');
        $sanitized['filter'] = isset($settings['filter']) ? $this->sanitize_shortcode_value($settings['filter']) : '';
        $sanitized['spacing'] = isset($settings['spacing']) ? $this->sanitize_shortcode_value($settings['spacing']) : '';
        $sanitized['columns'] = $this->sanitize_count($settings, 'columns');
        $sanitized['daterange'] = $this->sanitize_pattern($settings, 'daterange', '/^\d{4}-\d{2}-\d{2} - \d{4}-\d{2}-\d{2}$/');
        // 'none' is the overlay control's way of saying "don't emit the attribute".
        $overlay = $this->sanitize_option($settings, 'overlay');
        $sanitized['overlay'] = $overlay === 'none' ? '' : $overlay;
        // Shares juicer_sanitize_after_callback() with the plain [juicer] shortcode path,
        // so the widget and a hand-written shortcode accept exactly the same callbacks.
        $sanitized['after'] = isset($settings['after'])
            ? juicer_sanitize_after_callback(sanitize_text_field($settings['after']))
            : '';

        return $sanitized;
    }

    private function sanitize_count($settings, $key) {
        return isset($settings[$key]) ? absint($settings[$key]) : '';
    }

    // Only values the control actually offers; anything else is dropped rather than
    // replaced with the default, so an unrecognised value is never silently rewritten
    // into a different one.
    private function sanitize_option($settings, $key) {
        if (isset($settings[$key]) && in_array($settings[$key], $this->get_control_option_keys($key), true)) {
            return $settings[$key];
        }

        return '';
    }

    // Only the shape the control documents; anything else is dropped.
    private function sanitize_pattern($settings, $key, $pattern) {
        if (!isset($settings[$key])) {
            return '';
        }
        $value = sanitize_text_field($settings[$key]);

        return preg_match($pattern, $value) ? $value : '';
    }

    // sanitize_text_field() leaves quotes and brackets intact, which are exactly the
    // characters that terminate an attribute or the shortcode itself once the value is
    // concatenated into the shortcode string. A filter of 'Insta" data-x="y' would
    // otherwise close the filter attribute and add an attacker-chosen one. None of these
    // characters are meaningful in a filter or spacing value, so they are simply removed.
    private function sanitize_shortcode_value($value) {
        return str_replace(['"', "'", '[', ']'], '', sanitize_text_field($value));
    }

    // Single source of truth for the select controls' allowed values: _register_controls()
    // builds the editor dropdowns from these, and sanitize_settings() validates against the
    // same lists. Elementor's get_controls() cannot be used for this -- at render time it
    // returns the control without its 'options' key.
    private function get_control_options($control_id) {
        switch ($control_id) {
            case 'style':
                return [
                    'modern' => __('Modern', 'juicer'),
                    'night' => __('Night', 'juicer'),
                    'polaroid' => __('Polaroid', 'juicer'),
                    'image_grid' => __('Image Grid', 'juicer'),
                    'widget' => __('Widget', 'juicer'),
                    'slider' => __('Slider (No YouTube support)', 'juicer'),
                    'hip' => __('Hip', 'juicer'),
                    'living_wall' => __('Living Wall', 'juicer'),
                ];
            case 'overlay':
                return [
                    'none' => __('None', 'juicer'),
                    'true' => __('True', 'juicer'),
                    'false' => __('False', 'juicer'),
                ];
        }

        return [];
    }

    private function get_control_option_keys($control_id) {
        return array_keys($this->get_control_options($control_id));
    }

    protected function content_template() {}

    // No get_script_depends()/get_style_depends(): the date picker binds to a widget-panel
    // control that does not exist in rendered output, so declaring it here would make
    // Elementor load the library on public pages for no benefit. The editor enqueues it
    // directly via juicer_enqueue_daterangepicker_in_editor().
}
?>

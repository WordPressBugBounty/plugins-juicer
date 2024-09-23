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
                'options' => [
                    'modern' => __('Modern', 'juicer'),
                    'night' => __('Night', 'juicer'),
                    'polaroid' => __('Polaroid', 'juicer'),
                    'image_grid' => __('Image Grid', 'juicer'),
                    'widget' => __('Widget', 'juicer'),
                    'slider' => __('Slider (No YouTube support)', 'juicer'),
                    'hip' => __('Hip', 'juicer'),
                    'living_wall' => __('Living Wall', 'juicer')
                ],
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
                'options' => [
                    'none' => __('None', 'juicer'),
                    'true' => __('True', 'juicer'),
                    'false' => __('False', 'juicer'),
                ],
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
            $shortcode = $settings['shortcode'];
            
            $shortcode_attributes = [
                'per' => ' per="' . $settings['per'] . '"',
                'pages' => ' pages="' . $settings['pages'] . '"',
                'truncate' => ' truncate="' . $settings['truncate'] . '"',
                'style' => ' style="' . $settings['style'] . '"',
                'filter' => ' filter="' . $settings['filter'] . '"',
                'spacing' => ' spacing="' . $settings['spacing'] . '"',
                'columns' => ' columns="' . $settings['columns'] . '"'
            ];

            // Handle date range
            if (!empty($settings['daterange'])) {
                $shortcode_attributes['daterange'] = ' daterange="' . $settings['daterange'] . '"';
            }

            // Handle overlay
            if ($settings['overlay'] !== 'none') {
                $shortcode_attributes['overlay'] = ' overlay="' . $settings['overlay'] . '"';
            }

            // Handle after
            if (!empty($settings['after'])) {
                $shortcode_attributes['after'] = ' after="' . $settings['after'] . '"';
            }

            foreach ($shortcode_attributes as $attribute => $value) {
                if (!empty($settings[$attribute])) {
                    $shortcode = str_replace(']', $value . ']', $shortcode);
                }
            }

            echo do_shortcode($shortcode);
        }
    }

    protected function content_template() {}

    public function get_script_depends() {
        return ['moment-js', 'daterangepicker', 'juicer-daterangepicker-init'];
    }

    public function get_style_depends() {
        return ['daterangepicker-css'];
    }
}
?>

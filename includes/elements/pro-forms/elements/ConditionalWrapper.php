<?php

namespace Bricks;

use \Bricksforge\ProForms\Helper as Helper;

if (!defined('ABSPATH'))
    exit;

class Brf_Pro_Forms_ConditionalWrapper extends \Bricks\Element
{

    public $category = 'bricksforge forms';
    public $name = 'brf-pro-forms-field-conditional-wrapper';
    public $icon = 'fa-solid fa-lightbulb';
    public $css_selector = '';
    public $scripts = [];
    public $nestable = true;

    public function get_label()
    {
        return esc_html__("Conditional Wrapper", 'bricksforge');
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script('bricksforge-elements');
    }

    public function set_control_groups()
    {
        $this->control_groups['conditions'] = [
            'title'    => esc_html__('Conditions', 'bricksforge'),
            'tab'      => 'content',
        ];
    }

    public function set_controls()
    {
        // Tag name
        $this->controls['tag_name'] = [
            'label'          => esc_html__('Tag Name', 'bricksforge'),
            'type'           => 'text',
            'default'        => 'div',
        ];

        $this->controls = array_merge($this->controls, Helper::get_condition_controls());
    }

    public function render()
    {
        $settings = $this->settings;
        $tag_name = isset($settings['tag_name']) ? $settings['tag_name'] : 'div';

        // Conditions
        if (isset($settings['hasConditions']) && isset($settings['conditions']) && $settings['conditions']) {
            $this->set_attribute('_root', 'data-brf-conditions', json_encode($settings['conditions']));
        }
        if (isset($settings['conditionsRelation']) && $settings['conditionsRelation']) {
            $this->set_attribute('_root', 'data-brf-conditions-relation', $settings['conditionsRelation']);
        }

        $output = '<' . $tag_name . ' ' . $this->render_attributes('_root') . '>';

        $output .= Frontend::render_children($this);

        $output .= '</' . $tag_name . '>';

        echo $output;
?>
<?php
    }
}

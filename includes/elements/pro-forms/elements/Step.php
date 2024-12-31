<?php

namespace Bricks;

use \Bricksforge\ProForms\Helper as Helper;

if (!defined('ABSPATH'))
    exit;

class Brf_Pro_Forms_Step extends \Bricks\Element
{

    public $category = 'bricksforge forms';
    public $name = 'brf-pro-forms-field-step';
    public $icon = 'fa-solid fa-list-check';
    public $css_selector = '';
    public $scripts = [];
    public $nestable = false;
    private $turnstile_key;

    public function get_label()
    {
        return esc_html__("Step", 'bricksforge');
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script('bricksforge-elements');
    }

    public function set_control_groups()
    {
        $this->control_groups['general'] = [
            'title'    => esc_html__('General', 'bricksforge'),
            'tab'      => 'content',
        ];
    }

    public function set_controls()
    {
        $this->controls['label'] = [
            'group' => 'general',
            'label'          => esc_html__('Label', 'bricksforge'),
            'type'           => 'text',
            'inline'         => true,
            'spellcheck'     => false,
            'hasDynamicData' => true,
            'default'   => 'Step'
        ];

        $this->controls = array_merge($this->controls, Helper::get_advanced_controls());
    }

    public function render()
    {
        $settings = $this->settings;
        $parent_settings = Helper::get_nestable_parent_settings($this->element) ? Helper::get_nestable_parent_settings($this->element) : [];

        /**
         * Wrapper
         */
        $this->set_attribute("_root", 'class', ['step']);
        $this->set_attribute("_root", 'aria-label', isset($settings['label']) ? $settings['label'] : '');

        if (bricks_is_builder() || bricks_is_rest_call()) {
            $this->set_attribute("_root", 'data-step-builder');
        }

        $output = '<div ' . $this->render_attributes('_root') . '>';

        if (bricks_is_builder() || bricks_is_rest_call()) {
            $output .= 'Step: ' . $settings['label'];
        }

        $output .= '</div>';

        echo $output;
?>
<?php
    }
}

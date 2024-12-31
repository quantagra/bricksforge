<?php

namespace Bricksforge\ProForms\Actions;

use Bricksforge\Api\FormsHelper as FormsHelper;

class Update_Option
{
    public $name = "update_option";


    public function run($form)
    {

        $forms_helper = new FormsHelper();
        $form_settings = $form->get_settings();

        $option_data = $form_settings['pro_forms_post_action_option_update_option_data'];

        $option_data = array_map(function ($item) {
            return array(
                'id' => isset($item['id']) ? $item['id'] : '',
                'name'         => isset($item['name']) ? bricks_render_dynamic_data($item['name']) : '',
                'value'        => isset($item['value']) ? bricks_render_dynamic_data($item['value']) : '',
                'type'         => isset($item['type']) ? $item['type'] : '',
                'selector'     => isset($item['selector']) ? $item['selector'] : '',
                'number_field' => isset($item['number_field']) ? bricks_render_dynamic_data($item['number_field'], $post_id) : '',
            );
        }, $option_data);

        $updated_values = array();

        // Update Option for each $option_data
        foreach ($option_data as $option) {
            $option_name = $option['name'];
            $option_value = $option['value'];
            $option_type = $option['type'];
            $option_selector = $option['selector'];
            $option_number_field = $option['number_field'];

            if (!isset($option_name) || !isset($option_value)) {
                continue;
            }

            $option_name = $form->get_form_field_by_id($option_name);
            $option_value = $form->get_form_field_by_id($option_value);

            $new_option_value;
            $current_value = get_option($option_name);

            switch ($option_type) {
                case 'replace':
                    $new_option_value = $option_value;
                    break;
                case 'increment':
                    $new_option_value = intval($current_value) + 1;
                    break;
                case 'decrement':
                    $new_option_value = intval($current_value) - 1;
                    break;
                case 'increment_by_number':
                    $option_number_field = $form->get_form_field_by_id($option_number_field);
                    $new_option_value = intval($current_value) + intval($option_number_field);
                    break;
                case 'decrement_by_number':
                    $option_number_field = $form->get_form_field_by_id($option_number_field);
                    $new_option_value = intval($current_value) - intval($option_number_field);
                    break;
                case 'add_to_array':
                    // If the current value is not an array, make it one and add the new value
                    if (!is_array($current_value)) {
                        if (empty($current_value)) {
                            $new_option_value = array($option_value); // Only add the new value if current value is empty
                        } else {
                            $new_option_value = array($current_value, $option_value);
                        }
                    } else {
                        $new_option_value = array_merge($current_value, array($option_value));
                    }
                    break;
                case 'remove_from_array':
                    // If the current value is not an array, make it one and remove the new value
                    if (is_array($current_value)) {
                        $new_option_value = array_diff($current_value, array($option_value));
                    }
                    break;
                default:
                    $new_option_value = $option_value;
                    break;
            }

            $new_option_value = $forms_helper->sanitize_value($new_option_value);

            $result = update_option($option_name, $new_option_value);

            if (!$result && ($new_option_value !== $current_value)) {
                $form->set_result(
                    [
                        'action' => $this->name,
                        'type'   => 'error',
                        'message' => esc_html__('Option could not be updated.', 'bricksforge'),
                    ]
                );
            }

            $allow_live_update = $option_type === 'add_to_array' || $option_type === 'remove_from_array' ? false : true;

            array_push(
                $updated_values,
                array(
                    'name'     => $option_name,
                    'value'    => $new_option_value,
                    'selector' => $option_selector,
                    'live'     => $allow_live_update,
                    'data' => $option
                )
            );
        }

        $form->set_result(
            [
                'action' => $this->name,
                'type'   => 'success',
            ]
        );

        return $updated_values;
    }
}

<?php

namespace Bricksforge\ProForms;

use Bricksforge\Helper\ElementsHelper as ElementsHelper;

if (!defined('ABSPATH')) {
    exit;
}

class Helper
{

    static function get_autocomplete_options()
    {
        return [
            "off" => "off",
            "on" => "on",
            "name" => "name",
            "honorific-prefix" => "honorific-prefix",
            "given-name" => "given-name",
            "additional-name" => "additional-name",
            "family-name" => "family-name",
            "honorific-suffix" => "honorific-suffix",
            "nickname" => "nickname",
            "email" => "email",
            "username" => "username",
            "new-password" => "new-password",
            "current-password" => "current-password",
            "one-time-code" => "one-time-code",
            "organization-title" => "organization-title",
            "organization" => "organization",
            "street-address" => "street-address",
            "address-line1" => "address-line1",
            "address-line2" => "address-line2",
            "address-line3" => "address-line3",
            "address-level4" => "address-level4",
            "address-level3" => "address-level3",
            "address-level2" => "address-level2",
            "address-level1" => "address-level1",
            "country" => "country",
            "country-name" => "country-name",
            "postal-code" => "postal-code",
            "cc-name" => "cc-name",
            "cc-given-name" => "cc-given-name",
            "cc-additional-name" => "cc-additional-name",
            "cc-family-name" => "cc-family-name",
            "cc-number" => "cc-number",
            "cc-exp" => "cc-exp",
            "cc-exp-month" => "cc-exp-month",
            "cc-exp-year" => "cc-exp-year",
            "cc-csc" => "cc-csc",
            "cc-type" => "cc-type",
            "transaction-currency" => "transaction-currency",
            "transaction-amount" => "transaction-amount",
            "language" => "language",
            "bday" => "bday",
            "bday-day" => "bday-day",
            "bday-month" => "bday-month",
            "bday-year" => "bday-year",
            "sex" => "sex",
            "tel" => "tel",
            "tel-country-code" => "tel-country-code",
            "tel-national" => "tel-national",
            "tel-area-code" => "tel-area-code",
            "tel-local" => "tel-local",
            "tel-local-prefix" => "tel-local-prefix",
            "tel-local-suffix" => "tel-local-suffix",
            "tel-extension" => "tel-extension",
            "impp" => "impp",
            "url" => "url",
            "photo" => "photo",
            "webauthn" => "webauthn",
        ];
    }

    static function get_submit_conditions()
    {
        return [
            'option'                   => esc_html__('Database: Option', 'bricksforge'),
            'post_meta'                => esc_html__('Post Meta Field', 'bricksforge'),
            'storage_item'             => esc_html__('Storage Item', 'bricksforge'),
            'form_field'               => esc_html__('Form Field', 'bricksforge'),
            'submission_count_reached' => esc_html__('Submission Limit Reached', 'bricksforge'),
            'submission_field'         => esc_html__('Submission Field (ID)', 'bricksforge'),
        ];
    }

    static function get_field_conditions()
    {
        return [
            'form_field'               => esc_html__('Form Field', 'bricksforge'),
            'storage_item'             => esc_html__('Storage Item', 'bricksforge'),
        ];
    }

    static function get_condition_operators()
    {
        return [
            '=='           => esc_html__('Is Equal', 'bricksforge'),
            '!='           => esc_html__('Is Not Equal', 'bricksforge'),
            '>'            => esc_html__('Is Greater Than', 'bricksforge'),
            '>='           => esc_html__('Is Greater Than or Equal', 'bricksforge'),
            '<'            => esc_html__('Is Less Than', 'bricksforge'),
            '<='           => esc_html__('Is Less Than or Equal', 'bricksforge'),
            'contains'     => esc_html__('Contains', 'bricksforge'),
            'not_contains' => esc_html__('Not Contains', 'bricksforge'),
            'starts_with'  => esc_html__('Starts With', 'bricksforge'),
            'ends_with'    => esc_html__('Ends With', 'bricksforge'),
            'empty'        => esc_html__('Is Empty', 'bricksforge'),
            'not_empty'    => esc_html__('Is Not Empty', 'bricksforge'),
            'exists'       => esc_html__('Exists', 'bricksforge'),
            'not_exists'   => esc_html__('Not Exists', 'bricksforge'),
        ];
    }

    static function get_condition_data_types()
    {
        return [
            'string' => esc_html__('String', 'bricksforge'),
            'number' => esc_html__('Number', 'bricksforge'),
            'array'  => esc_html__('Array', 'bricksforge'),
        ];
    }

    static function get_loop_controls()
    {
        $controls = [];

        $controls['hasLoop'] = [
            'label' => esc_html__('Use query loop', 'bricksforge'),
            'type'  => 'checkbox',
        ];

        $controls['query'] = [
            'label'    => esc_html__('Query', 'bricksforge'),
            'type'     => 'query',
            'popup'    => true,
            'inline'   => true,
            'required' => [
                'hasLoop',
                '=',
                true,
            ],
        ];

        return $controls;
    }

    static function get_selected_controls()
    {
        $controls = [];

        $controls['conditionallySelected'] = [
            'group' => 'general',
            'type'  => 'checkbox',
            'inline' => true,
            'label' => esc_html__('Conditionally Selected', 'bricksforge'),
        ];

        // Selected If
        $controls['selectedIf'] = [
            'group' => 'general',
            'type'  => 'select',
            'label' => esc_html__('Selected If', 'bricksforge'),
            'options' => [
                'value' => esc_html__('Option Value Is', 'bricksforge'),
                'taxonomy' => esc_html__('Is Enabled Post Taxonomy Term', 'bricksforge'),
            ],
            'required' => [['conditionallySelected', '=', true]],
        ];

        $controls['selectedIfValue'] = [
            'group' => 'general',
            'type'  => 'text',
            'label' => esc_html__('Value', 'bricksforge'),
            'required' => [['selectedIf', '=', 'value']],
        ];

        $controls['selectedIfPostId'] = [
            'group' => 'general',
            'type'  => 'text',
            'label' => esc_html__('Post ID', 'bricksforge'),
            'required' => [['selectedIf', '=', 'taxonomy']],
        ];

        $controls['selectedIfTaxonomy'] = [
            'group' => 'general',
            'type'  => 'text',
            'label' => esc_html__('Taxonomy Key', 'bricksforge'),
            'required' => [['selectedIf', '=', 'taxonomy']],
        ];

        return $controls;
    }

    static function get_checked_controls()
    {
        $controls = [];
        $controls['checked'] = [
            'group' => 'general',
            'type'  => 'checkbox',
            'label' => esc_html__('Checked', 'bricksforge'),
        ];

        $controls['conditionallyChecked'] = [
            'group' => 'general',
            'type'  => 'checkbox',
            'inline' => true,
            'label' => esc_html__('Conditionally Checked', 'bricksforge'),
        ];

        // Checked If
        $controls['checkedIf'] = [
            'group' => 'general',
            'type'  => 'select',
            'label' => esc_html__('Checked If', 'bricksforge'),
            'options' => [
                'value' => esc_html__('Checkbox Value Is', 'bricksforge'),
                'taxonomy' => esc_html__('Is Enabled Post Taxonomy Term', 'bricksforge'),
            ],
            'required' => [['conditionallyChecked', '=', true]],
        ];

        $controls['checkedIfValue'] = [
            'group' => 'general',
            'type'  => 'text',
            'label' => esc_html__('Value', 'bricksforge'),
            'required' => [['checkedIf', '=', 'value']],
        ];

        $controls['checkedIfPostId'] = [
            'group' => 'general',
            'type'  => 'text',
            'label' => esc_html__('Post ID', 'bricksforge'),
            'required' => [['checkedIf', '=', 'taxonomy']],
        ];

        $controls['checkedIfTaxonomy'] = [
            'group' => 'general',
            'type'  => 'text',
            'label' => esc_html__('Taxonomy Key', 'bricksforge'),
            'required' => [['checkedIf', '=', 'taxonomy']],
        ];

        return $controls;
    }

    static function get_default_controls($field_type = '')
    {
        $controls = [];

        $default_width = '100%';
        $default_width_selector = 'input';

        if ($field_type == 'checkbox' || $field_type == 'radio') {
            $default_width = 'auto';
            $default_width_selector = '&';
        }

        if ($field_type == 'checkbox_wrapper' || $field_type == 'radio_wrapper') {
            $default_width_selector = '&';
        }

        if ($field_type == 'file') {
            $default_width_selector = '.choose-files';
        }

        $needs_width = !in_array($field_type, ['checkbox', 'radio', 'image-checkbox', 'image-radio', 'card-checkbox', 'card-radio', 'signature']);
        $needs_initial_value = !in_array($field_type, ['checkbox_wrapper', 'radio_wrapper', 'signature']);
        $needs_required = !in_array($field_type, ['checkbox_wrapper', 'radio_wrapper', 'hidden']);
        $needs_custom_required = in_array($field_type, ['checkbox_wrapper', 'radio_wrapper']);
        $needs_custom_required_count = in_array($field_type, ['checkbox_wrapper']);
        $needs_icon = !in_array($field_type, ['file', 'checkbox_wrapper', 'radio_wrapper', 'hidden', 'checkbox', 'radio', 'card-checkbox', 'card-radio']);
        $needs_custom_id = !in_array($field_type, ['checkbox', 'radio', 'card-checkbox', 'card-radio', 'image-checkbox', 'image-radio']);
        $needs_pattern = in_array($field_type, ['text', 'email', 'number', 'tel', 'url', 'password', 'textarea']);
        $needs_style = in_array($field_type, ['text', 'email', 'number', 'tel', 'url', 'password', 'textarea', 'calculation', 'date', 'select', 'colorPicker']);
        $needs_disabled = in_array($field_type, ['text', 'email', 'number', 'tel', 'url', 'password', 'textarea', 'calculation', 'date', 'select']);
        $next_step_triggers = in_array($field_type, ['radio', 'card-radio', 'image-radio']);
        $needs_masking = in_array($field_type, ['text']);
        $needs_context = true;
        $needs_calculation_value = in_array($field_type, ['checkbox', 'radio', 'card-checkbox', 'card-radio', 'image-checkbox', 'image-radio', 'option']);

        $initial_value_default = '';

        // If types like checkboxes or radios or options, set the initial value to "value"
        if (in_array($field_type, ['checkbox', 'radio', 'card-checkbox', 'card-radio', 'image-checkbox', 'image-radio'])) {
            $initial_value_default = 'Value';
        }

        // ID
        $id_description = esc_html__('The ID is used to identify the field in the form submission. If not set, the element ID will be used.', 'bricksforge');

        if ($needs_custom_id) {
            $controls['id'] = [
                'group' => 'general',
                'label'          => esc_html__('Custom ID', 'bricksforge'),
                'description'    => $id_description,
                'type'           => 'text',
                'inline'         => true,
                'spellcheck'     => false,
                'hasDynamicData' => true,
                'default' => \Bricks\Helpers::generate_random_id(false)
            ];
        }

        if ($needs_masking) {
            $controls['enableInputMasking'] = [
                'group' => 'inputMasking',
                'label' => esc_html__('Input Masking', 'bricksforge'),
                'type'  => 'checkbox',
                'default' => false,
                'description' => esc_html__('Enable input masking.', 'bricksforge'),
            ];
            $controls['inputMaskingMethod'] = [
                'group' => 'inputMasking',
                'label' => esc_html__('Input Masking Method', 'bricksforge'),
                'type'  => 'select',
                'options' => [
                    'pattern' => esc_html__('Pattern', 'bricksforge'),
                ],
                'description' => esc_html__('Enter the input masking method.', 'bricksforge'),
                'required' => [['enableInputMasking', '=', true]],
            ];

            // Mask
            $controls['maskPattern'] = [
                'group' => 'inputMasking',
                'label' => esc_html__('Mask', 'bricksforge'),
                'type'  => 'text',
                'description' => esc_html__('Enter the input mask. 0 = Any Digit, a = Any Letter, * = Any Char ', 'bricksforge'),
                'required' => [['enableInputMasking', '=', true], ['inputMaskingMethod', '=', 'pattern']],
            ];

            // Repeater for Definitions
            $controls['maskDefinitions'] = [
                'group' => 'inputMasking',
                'label' => esc_html__('Mask Definitions', 'bricksforge'),
                'type'  => 'repeater',
                'titleProperty' => 'name',
                'fields' => [
                    'name' => [
                        'label' => esc_html__('Character', 'bricksforge'),
                        'type'  => 'text',
                    ],
                    'mask' => [
                        'label' => esc_html__('Mask', 'bricksforge'),
                        'type'  => 'text',
                        'placeholder' => '*',
                    ],
                    'placeholderChar' => [
                        'label' => esc_html__('Placeholder Character', 'bricksforge'),
                        'type'  => 'text',
                        'placeholder' => '_',
                    ],
                ],
                'required' => [['enableInputMasking', '=', true], ['inputMaskingMethod', '=', 'pattern']],
            ];

            // Lazy
            $controls['lazyMasking'] = [
                'group' => 'inputMasking',
                'label' => esc_html__('Lazy', 'bricksforge'),
                'type'  => 'checkbox',
                'default' => false,
                'description' => esc_html__('If checked, the mask will be applied only after the user starts typing.', 'bricksforge'),
                'required' => [['enableInputMasking', '=', true]],
            ];
        }

        // Pattern
        if ($needs_pattern) {
            $controls['pattern'] = [
                'group' => 'general',
                'label'          => esc_html__('Pattern', 'bricksforge'),
                'description'    => esc_html__('Expects a regular expression. (For example: [56]*)', 'bricksforge'),
                'type'           => 'text',
                'inline'         => true,
                'spellcheck'     => false,
                'hasDynamicData' => true,
            ];
        }

        // Label
        if ($field_type != "hidden") {
            $controls['label'] = [
                'group' => 'general',
                'label'          => esc_html__('Label', 'bricksforge'),
                'type'           => 'text',
                'inline'         => true,
                'spellcheck'     => false,
                'hasDynamicData' => true,
                'default'        => esc_html__('Label', 'bricksforge'),
            ];
        }

        // Show Label
        if ($field_type != "hidden") {
            $controls['showLabel'] = [
                'group' => 'general',
                'label'          => esc_html__('Show Label', 'bricksforge'),
                'type'           => 'checkbox',
                'default'        => true,
            ];
        }

        // Initial Value
        if ($needs_initial_value) {
            $controls['value'] = [
                'group' => 'general',
                'label'          => esc_html__('Value', 'bricksforge'),
                'type'           => 'text',
                'inline'         => true,
                'spellcheck'     => false,
                'hasDynamicData' => true,
                'default'        => $initial_value_default,
            ];
        }

        if ($needs_calculation_value) {
            $controls['calculationValue'] = [
                'group' => 'general',
                'label'          => esc_html__('Calculation Value', 'bricksforge'),
                'type'           => 'number',
                'inline'         => true,
                'hasDynamicData' => true,
                'description'    => esc_html__('A numeric value that will be used for calculation fields.', 'bricksforge'),
            ];
        }

        if ($needs_context) {
            $controls['postContext'] = [
                'group' => 'general',
                'label'          => esc_html__('Context', 'bricksforge'),
                'type'           => 'text',
                'inline'         => true,
                'hasDynamicData' => true,
                'description'    => esc_html__('If you dont get the data you expect, it sometimes helps to include a post id as context. This field allows dynamic data.', 'bricksforge'),
            ];
        }

        // Width
        if ($needs_width) {
            $controls['width'] = [
                'group' => 'general',
                'label'          => esc_html__('Width', 'bricksforge'),
                'type'           => 'text',
                'inline'         => true,
                'spellcheck'     => false,
                'hasDynamicData' => true,
                'default'        => $default_width,
                'rerender' => true,
                'css' => [
                    [
                        'property' => 'width',
                        'selector' => $default_width_selector
                    ],
                ],
            ];
        }

        if ($field_type == 'textarea') {

            // Height
            $controls['height'] = [
                'group'    => 'general',
                'label'    => esc_html__('Height', 'bricksforge'),
                'type'     => 'number',
                'units'    => true,
                'css'      => [
                    [
                        'property' => 'height',
                    ],
                ],
            ];
        }

        // Required
        if ($needs_required) {
            $controls['required'] = [
                'group' => 'general',
                'label'          => esc_html__('Required', 'bricksforge'),
                'type'           => 'checkbox',
                'default'        => false,
                'description'    => esc_html__('If checked, the field will be required.', 'bricksforge'),
            ];
        }

        // Custom Required
        if ($needs_custom_required) {
            $controls['customRequired'] = [
                'group' => 'general',
                'label'          => esc_html__('Required', 'bricksforge'),
                'type'           => 'checkbox',
                'default'        => false,
                'description'    => esc_html__('If checked, the field will be required.', 'bricksforge'),
            ];
        }

        // Required Count
        if ($needs_custom_required_count) {
            $controls['customRequiredCount'] = [
                'group' => 'general',
                'label'          => esc_html__('Required Count', 'bricksforge'),
                'type'           => 'number',
                'default'        => 1,
                'description'    => esc_html__('The minimum number of checkboxes that must be checked.', 'bricksforge'),
                'required' => [['customRequired', '=', true]],
            ];
        }

        if ($needs_icon) {
            $controls['icon'] = [
                'group' => 'general',
                'label' => esc_html__('Icon', 'bricksforge'),
                'type'  => 'icon',
            ];
        }

        // Disabled
        if ($needs_disabled) {
            $controls['disabled'] = [
                'group' => 'general',
                'label'          => esc_html__('Disabled', 'bricksforge'),
                'type'           => 'checkbox',
                'default'        => false,
                'description'    => esc_html__('If checked, the field will be disabled.', 'bricksforge'),
            ];
        }

        // Triggers Next Step
        if ($next_step_triggers) {
            $controls['nextStepTrigger'] = [
                'group' => 'general',
                'label'          => esc_html__('Triggers Next Step', 'bricksforge'),
                'type'           => 'checkbox',
                'default'        => false,
                'description'    => esc_html__('If checked, the field will trigger the next step.', 'bricksforge'),
            ];
        }

        /**
         * Style
         */
        if ($needs_style) {
            // Background
            $controls['background'] = [
                'group' => 'style',
                'label'          => esc_html__('Background', 'bricksforge'),
                'type'           => 'background',
                'css' => [
                    [
                        'property' => 'background',
                        'selector' => '&.form-group input[name*="form-field-"], input[name*="brfr"], textarea[name*="brfr"], select[name*="brfr"], textarea[name*="form-field-"], select[name*="form-field-"], .choices, .choices .choices__inner, .choices .choices__item, .choices, .choices[data-type*=select-one] .choices__input, .choices__list--dropdown',
                    ],
                ],
            ];

            // Typography
            $controls['typography'] = [
                'group' => 'style',
                'label'          => esc_html__('Typography', 'bricksforge'),
                'type'           => 'typography',
                'css' => [
                    [
                        'property' => 'typography',
                        'selector' => '&.form-group input[name*="form-field-"], textarea[name*="form-field-"], select[name*="form-field-"], input[name*="brfr"], textarea[name*="brfr"], select[name*="brfr"], .choices .choices__inner, .choices .choices__item, .choices, .choices[data-type*=select-one] .choices__input',
                    ],
                ],
            ];

            // Padding
            $controls['padding'] = [
                'group' => 'style',
                'label'          => esc_html__('Padding', 'bricksforge'),
                'type'           => 'spacing',
                'css' => [
                    [
                        'property' => 'padding',
                        'selector' => '&.form-group input[name*="form-field-"], textarea[name*="form-field-"], select[name*="form-field-"], input[name*="brfr"], textarea[name*="brfr"], select[name*="brfr"], .choices .choices__inner',
                    ],
                ],
            ];

            // Border
            $controls['border'] = [
                'group' => 'style',
                'label'          => esc_html__('Border', 'bricksforge'),
                'type'           => 'border',
                'css' => [
                    [
                        'property' => 'border',
                        'selector' => '&.form-group input[name*="form-field-"], textarea[name*="form-field-"], select[name*="form-field-"],input[name*="brfr"], textarea[name*="brfr"], select[name*="brfr"], .choices .choices__inner',
                    ],
                ],
            ];

            // Box Shadow
            $controls['boxShadow'] = [
                'group' => 'style',
                'label'          => esc_html__('Box Shadow', 'bricksforge'),
                'type'           => 'box-shadow',
                'css' => [
                    [
                        'property' => 'box-shadow',
                        'selector' => '&.form-group input[name*="form-field-"], textarea[name*="form-field-"], select[name*="form-field-"],input[name*="brfr"], textarea[name*="brfr"], select[name*="brfr"], .choices .choices__inner, .choices, .choices[data-type*=select-one] .choices__input',
                    ],
                ],
            ];

            // Transform
            $controls['transform'] = [
                'group' => 'style',
                'label'          => esc_html__(' Transform', 'bricksforge'),
                'type'           => 'transform',
                'css' => [
                    [
                        'property' => 'transform',
                        'selector' => '&.form-group input[name*="form-field-"], textarea[name*="form-field-"], select[name*="form-field-"],input[name*="brfr"], textarea[name*="brfr"], select[name*="brfr"], .choices .choices__inner, .choices .choices__item, .choices, .choices[data-type*=select-one] .choices__input',
                    ],
                ],
            ];
        }

        return $controls;
    }

    static function get_validation_controls()
    {
        $controls = [];

        $controls['validation'] = [
            'group' => 'validation',
            'label' => esc_html__('Validation Rules', 'bricksforge'),
            'type'  => 'repeater',
            'titleProperty' => 'type',
            'fields' => [
                // Type
                'type' => [
                    'label' => esc_html__('Validation Type', 'bricksforge'),
                    'type'  => 'select',
                    'options' => [
                        'required' => esc_html__('Required', 'bricksforge'),
                        'email' => esc_html__('Email', 'bricksforge'),
                        'number' => esc_html__('Number', 'bricksforge'),
                        'url' => esc_html__('URL', 'bricksforge'),
                        'minChars' => esc_html__('Minimum Characters', 'bricksforge'),
                        'maxChars' => esc_html__('Maximum Characters', 'bricksforge'),
                        'value' => esc_html__('Value', 'bricksforge'),
                        'custom' => esc_html__('Custom (Regex)', 'bricksforge'),
                    ],
                ],

                // Chars
                'charsCount' => [
                    'label' => esc_html__('Characters', 'bricksforge'),
                    'type'  => 'number',
                    'required' => [['type', '=', ['minChars', 'maxChars']]],
                ],

                // Value
                'value' => [
                    'label' => esc_html__('Value', 'bricksforge'),
                    'type'  => 'text',
                    'required' => [['type', '=', ['value']]],
                ],

                // Custom (Regex)
                'regex' => [
                    'label' => esc_html__('Regex', 'bricksforge'),
                    'type'  => 'text',
                    'required' => [['type', '=', 'custom']],
                ],

                // Message
                'message' => [
                    'label' => esc_html__('Validation Message', 'bricksforge'),
                    'type'  => 'text',
                ],
            ],
        ];

        $controls['showMessageBelowField'] = [
            'group' => 'validation',
            'label' => esc_html__('Show Message Below Field', 'bricksforge'),
            'type'  => 'checkbox',
            'description' => esc_html__('If checked, the validation message will be shown below the field instead as alert below the form.', 'bricksforge'),
        ];

        $controls['enableLiveValidation'] = [
            'group' => 'validation',
            'label' => esc_html__('Live Validation', 'bricksforge'),
            'type'  => 'checkbox',
            'description' => esc_html__('Validates form fields on blur.', 'bricksforge'),
        ];

        // Show Message
        $controls['showValidationMessage'] = [
            'group' => 'validation',
            'label' => esc_html__('Show Validation Message', 'bricksforge'),
            'type'  => 'checkbox',
            'required' => [['enableLiveValidation', '=', true]],
        ];

        $controls['validationMessageTypography'] = [
            'group' => 'validation',
            'label' => esc_html__('Message Typography', 'bricksforge'),
            'type'  => 'typography',
            'required' => [['showValidationMessage', '=', true], ['enableLiveValidation', '=', true]],
            'css' => [
                [
                    'property' => 'typography',
                    'selector' => '.brf-validation-message',
                ],
            ],
        ];

        $controls['validationFieldBorder'] = [
            'group' => 'validation',
            'label' => esc_html__('Invalid Field Border', 'bricksforge'),
            'type'  => 'border',
            'required' => [['showValidationMessage', '=', true], ['enableLiveValidation', '=', true]],
            'css' => [
                [
                    'property' => 'border',
                    'selector' => '.brf-invalid',
                ],
            ],
        ];

        return $controls;
    }
    static function get_accessibility_controls()
    {
        $controls = [];

        // Outline (Accessibility)
        $controls['outline'] = [
            'group' => 'accessibility',
            'label'          => esc_html__('Focus Outline', 'bricksforge'),
            'type'           => 'text',
            'css' => [
                [
                    'property' => 'outline',
                    'selector' => 'input:focus-visible + label',
                ],
            ],
        ];

        // Border
        $controls['border'] = [
            'group' => 'accessibility',
            'label'          => esc_html__('Focus Border', 'bricksforge'),
            'type'           => 'border',
            'css' => [
                [
                    'property' => 'border',
                    'selector' => 'input:focus-visible + label',
                ],
            ],
        ];

        // Filter (Accessibility)
        $controls['filter'] = [
            'group' => 'accessibility',
            'label'          => esc_html__('Focus Filter', 'bricksforge'),
            'type'           => 'filters',
            'inline' => true,
            'css' => [
                [
                    'property' => 'filter',
                    'selector' => 'input:focus-visible + label',
                ],
            ],
        ];

        // Transform (Accessibility)
        $controls['transform'] = [
            'group' => 'accessibility',
            'label'          => esc_html__('Focus Transform', 'bricksforge'),
            'type'           => 'transform',
            'css' => [
                [
                    'property' => 'transform',
                    'selector' => 'input:focus-visible + label',
                ],
            ],
        ];

        return $controls;
    }

    static function get_condition_controls()
    {
        $controls = [];


        $controls['hasConditions'] = [
            'group' => 'conditions',
            'label' => esc_html__('Add Conditions', 'bricksforge'),
            'type'  => 'checkbox',
            'default' => false,
        ];

        $controls['conditions'] = [
            'group' => 'conditions',
            'label' => esc_html__('Conditions', 'bricksforge'),
            'type'  => 'repeater',
            'titleProperty' => 'condition',
            'required' => [['hasConditions', '=', true]],
            'fields'        => [
                'postId'   => [
                    'label'       => esc_html__('Post ID', 'bricksforge'),
                    'type'        => 'text',
                    'placeholder' => 'Leave Empty For Current Post ID',
                    'required'    => [['condition', '=', 'post_meta']],
                ],
                'condition'         => [
                    'tab'     => 'content',
                    'group'   => 'submitButton',
                    'type'    => 'select',
                    'options' => self::get_field_conditions(),
                    'default' => 'option'
                ],

                'value'    => [
                    'required' => [['condition'], ['condition', '!=', 'submission_count_reached']],
                    'tab'      => 'content',
                    'group'    => 'submitButton',
                    'type'     => 'text',
                    'default'  => ''
                ],

                'operator' => [
                    'required' => [['value'], ['condition', '!=', 'submission_count_reached']],
                    'tab'      => 'content',
                    'group'    => 'submitButton',
                    'type'     => 'select',
                    'options'  => self::get_condition_operators(),
                    'default'  => '=='
                ],

                'value2'   => [
                    'required' => [['operator'], ['value', '!=', ''], ['condition', '!=', 'submission_count_reached'], ['operator', '!=', ['exists', 'not_exists', 'empty', 'not_empty']]],
                    'tab'      => 'content',
                    'group'    => 'submitButton',
                    'type'     => 'text',
                    'default'  => ''
                ],

                'type'     => [
                    'required' => [['condition', '!=', 'submission_count_reached']],
                    'tab'      => 'content',
                    'group'    => 'submitButton',
                    'label'    => esc_html__('Data Type', 'bricksforge'),
                    'type'     => 'select',
                    'options'  => self::get_condition_data_types(),
                    'default'  => 'string'
                ]
            ]
        ];

        $controls['conditionsRelation'] = [
            'group' => 'conditions',
            'label' => esc_html__('Conditions Relation', 'bricksforge'),
            'type'  => 'select',
            'required' => [['hasConditions', '=', true]],
            'options' => [
                'and' => esc_html__('AND', 'bricksforge'),
                'or'  => esc_html__('OR', 'bricksforge'),
            ],
            'default' => 'and'
        ];

        return $controls;
    }

    static function get_data_source_controls()
    {
        $controls = [];

        $controls['dataSourceCustom'] = [
            'group' => 'general',
            'label' => esc_html__('Static Data', 'bricksforge'),
            'type'  => 'repeater',
            'titleProperty' => 'label',
            'fields' => [
                'value' => [
                    'label' => esc_html__('Value', 'bricksforge'),
                    'type'  => 'text',
                ],
                'label' => [
                    'label' => esc_html__('Label', 'bricksforge'),
                    'type'  => 'text',
                ]
            ],
        ];

        $controls['dataSourceJson'] = [
            'group' => 'general',
            'label' => esc_html__('JSON Data', 'bricksforge'),
            'type'  => 'code',
            'description' => esc_html__('Enter a JSON array of objects. Each object can have a "value" and a "label" property. If you use single values, the value will be used as label.', 'bricksforge'),
        ];

        // If dataSourceJson is set, we offer alternative key label pairs
        $controls['dataSourceKeyLabel'] = [
            'group' => 'general',
            'label' => esc_html__('Alternative Label Key (JSON)', 'bricksforge'),
            'type'  => 'text',
            'description' => 'The default key is "label". Here, you can enter an alternative key to match the key in your JSON data.',
            'required' => [['dataSourceJson', '!=', '']],
        ];

        $controls['dataSourceKeyValue'] = [
            'group' => 'general',
            'label' => esc_html__('Alternative Value Key (JSON)', 'bricksforge'),
            'type'  => 'text',
            'description' => 'The default key is "value". Here, you can enter an alternative key to match the key in your JSON data.',
            'required' => [['dataSourceJson', '!=', '']],
        ];

        return $controls;
    }

    static function get_button_style_controls()
    {
        $controls = [];

        // Width
        $controls['width'] = [
            'group' => 'style',
            'label'          => esc_html__('Width', 'bricksforge'),
            'type'           => 'number',
            'units' => true,
            'css' => [
                [
                    'property' => 'width',
                ],
                [
                    'property' => 'width',
                    'selector' => 'button',
                    'value' => '100%'
                ],
            ],
        ];

        // Height
        $controls['height'] = [
            'group' => 'style',
            'label'          => esc_html__('Height', 'bricksforge'),
            'type'           => 'number',
            'units' => true,
            'css' => [
                [
                    'property' => 'height',
                    'selector' => 'button'
                ],
            ],
        ];

        // Background
        $controls['background'] = [
            'group' => 'style',
            'label'          => esc_html__('Background', 'bricksforge'),
            'type'           => 'background',
            'css' => [
                [
                    'property' => 'background',
                    'selector' => 'button'
                ],
            ],
        ];

        // Typography
        $controls['typography'] = [
            'group' => 'style',
            'label'          => esc_html__('Typography', 'bricksforge'),
            'type'           => 'typography',
            'css' => [
                [
                    'property' => 'typography',
                    'selector' => 'button'
                ],
            ],
        ];

        // Padding
        $controls['padding'] = [
            'group' => 'style',
            'label'          => esc_html__('Padding', 'bricksforge'),
            'type'           => 'spacing',
            'css' => [
                [
                    'property' => 'padding',
                    'selector' => 'button'
                ],
            ],
        ];

        // Border
        $controls['border'] = [
            'group' => 'style',
            'label'          => esc_html__('Border', 'bricksforge'),
            'type'           => 'border',
            'css' => [
                [
                    'property' => 'border',
                    'selector' => 'button'
                ],
            ],
        ];

        // Transform
        $controls['transform'] = [
            'group' => 'style',
            'label'          => esc_html__('Transform', 'bricksforge'),
            'type'           => 'transform',
            'css' => [
                [
                    'property' => 'transform',
                    'selector' => 'button'
                ],
            ],
        ];

        return $controls;
    }

    static function get_advanced_controls()
    {
        $controls = [];

        // Custom CSS Class
        $controls['cssClass'] = [
            'group' => 'general',
            'label' => esc_html__('CSS Class', 'bricksforge'),
            'type'  => 'text',
            'inline'         => true,
        ];

        return $controls;
    }

    static function get_nestable_parent_settings($element,  $depth = 0)
    {
        if ($depth > 10) { // Maximum recursion depth
            return false;
        }

        $parent_id = !empty($element['parent']) ? $element['parent'] : false;

        if (bricks_is_builder_call()) {
            // $elements selbst befüllen mit den children
        }

        if (isset($parent_id)) {
            $parent_element = !empty(\Bricks\Frontend::$elements[$parent_id]) ? \Bricks\Frontend::$elements[$parent_id] : false;

            if (!$parent_element) {
                foreach (ElementsHelper::$page_data as $element) {
                    if ($element['id'] == $parent_id) {
                        $parent_element = $element;
                        break;
                    }
                }
            }

            if (!$parent_element && bricks_is_builder_call()) {
                $post_id = get_the_ID();

                $parent_element = \Bricks\Helpers::get_element_data($post_id, $parent_id);

                if (isset($parent_element) && isset($parent_element['element'])) {
                    $parent_element = $parent_element['element'];
                }
            }

            // If there is no parent element, we stop here
            if (!isset($parent_element) || !$parent_element) {
                return false;
            }

            if ($parent_element['name'] === 'brf-pro-forms') {
                return $parent_element['settings'];
            } else {
                // Return the result of the recursive call
                return self::get_nestable_parent_settings($parent_element, $depth + 1);
            }
        }

        return false;
    }

    static function get_parent($name = "brf-pro-forms-field-checkbox-wrapper", $element = [], $depth = 0)
    {
        if ($depth > 10) { // Maximum recursion depth
            return false;
        }

        $parent_id = !empty($element['parent']) ? $element['parent'] : false;

        if (isset($parent_id)) {
            $parent_element = !empty(\Bricks\Frontend::$elements[$parent_id]) ? \Bricks\Frontend::$elements[$parent_id] : false;

            // If there is no parent element, we stop here
            if (!isset($parent_element) || !$parent_element) {
                return false;
            }

            if ($parent_element['name'] === $name) {
                return $parent_element;
            } else {
                // Return the result of the recursive call
                return self::get_nestable_parent_settings($parent_element, $depth + 1);
            }
        }

        return false;
    }

    static function parse_options($settings)
    {
        $options = [];

        // Custom
        if (isset($settings['dataSourceCustom']) && $settings['dataSourceCustom']) {
            foreach ($settings['dataSourceCustom'] as $option) {
                $options[] = [
                    'value' => $option['value'],
                    'label' => $option['label'],
                ];
            }
        }

        return $options;
    }

    static function get_quill_formats()
    {
        return [
            'header' => 'Headlines',
            'bold'           => 'Bold',
            'italic'         => 'Italic',
            'underline'      => 'Underline',
            'color' => 'Color',
            'background'       => 'Background Color',
            'strike'         => 'Strikethrough',
            'link' => 'Link',
            'code' => 'Code',

            'blockquote'     => 'Blockquote',
            'indent' => 'Indent',
            'outdent' => 'Outdent',
            'orderedList' => 'Ordered List',
            'bulletList' => 'Bullet List',
            'align' => 'Text Alignment',
            'direction' => 'Text Direction',
            'code-block' => 'Code Block',

            'image' => 'Image',
            'video' => 'Video'
        ];
    }

    static function get_color_palettes()
    {

        $palettes = get_option(BRICKS_DB_COLOR_PALETTE, []);

        if (empty($palettes)) {
            $palettes = \Bricks\Builder::default_color_palette();
        }

        // Extract the "name" field for each palette in the array
        $palette_names = array_column($palettes, 'name');

        // Create an array with the palette names as keys and the palette names as values
        $palette_names = array_combine($palette_names, $palette_names);

        return $palette_names;
    }
}

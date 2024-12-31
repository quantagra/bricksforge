<?php

namespace Bricks;

use \Bricksforge\ProForms\Helper as Helper;

if (!defined('ABSPATH'))
    exit;

class Brf_Pro_Forms_File extends \Bricks\Element
{

    public $category = 'bricksforge forms';
    public $name = 'brf-pro-forms-field-file';
    public $icon = 'fa-solid fa-file';
    public $css_selector = '';
    public $scripts = [];
    public $nestable = false;

    public function get_label()
    {
        return esc_html__("File", 'bricksforge');
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
        $this->control_groups['conditions'] = [
            'title'    => esc_html__('Conditions', 'bricksforge'),
            'tab'      => 'content',
        ];
    }

    public function set_controls()
    {
        $this->controls = array_merge($this->controls, Helper::get_default_controls('file'));

        $this->controls['fileUploadButtonText']   = [
            'group'    => 'general',
            'type'        => 'text',
            'placeholder' => esc_html__('Choose files', 'bricks'),
            'default'     => esc_html__('Choose files', 'bricks'),
        ];

        $this->controls['fileUploadLimit']        = [
            'group'    => 'general',
            'label'    => esc_html__('max. files', 'bricks'),
            'type'     => 'number',
            'min'      => 1,
            'max'      => 50,
        ];

        // Replace File (If 1)
        $this->controls['fileUploadReplace']      = [
            'group'    => 'general',
            'label'    => esc_html__('Replace file', 'bricks'),
            'type'     => 'checkbox',
            'default'  => false,
            'description' => esc_html__('If checked, the file will be replaced instead of added.', 'bricks'),
            'required' => ["fileUploadLimit", "=", "1"]
        ];

        $this->controls['fileUploadSize']         = [
            'group'    => 'general',
            'label'    => esc_html__('Max. size', 'bricks') . ' (MB)',
            'type'     => 'number',
            'min'      => 1,
            'max'      => 50,
        ];

        $this->controls['fileUploadAllowedTypes'] = [
            'group'    => 'general',
            'label'       => esc_html__('Allowed file types', 'bricks'),
            'placeholder' => 'pdf,jpg,...',
            'type'        => 'text',
        ];

        $this->controls['hideFileNamePreview'] = [
            'group'    => 'general',
            'label' => esc_html__('Hide file name text preview', 'bricks'),
            'type'  => 'checkbox',
            'default' => false,
            'description' => esc_html__('If checked, the file name preview will be hidden.', 'bricks'),
        ];

        $this->controls['hideImagePreview'] = [
            'group'    => 'general',
            'label' => esc_html__('Hide Image Preview', 'bricks'),
            'type'  => 'checkbox',
            'default' => true,
            'description' => esc_html__('If checked, the image preview will be hidden.', 'bricks'),
        ];

        // Allow Image Deletetion
        $this->controls['fileUploadDisableDeletion'] = [
            'group'    => 'general',
            'label'    => esc_html__('Disable file deletion', 'bricks'),
            'type'     => 'checkbox',
            'default'  => false,
            'description' => esc_html__('If checked, the file cannot be deleted after upload..', 'bricks'),
        ];

        // Preview Image Width
        $this->controls['fileUploadPreviewWidth'] = [
            'group'    => 'general',
            'label'    => esc_html__('Preview image width', 'bricks'),
            'type'     => 'number',
            'units' => true,
            'description' => esc_html__('The width of the preview images.', 'bricks'),
            'css' => [
                [
                    'property' => 'width',
                    'selector' => '.brf-field-image-preview-wrapper div, .brf-field-image-preview-wrapper img'
                ]
            ]
        ];

        // Preview Image Height
        $this->controls['fileUploadPreviewHeight'] = [
            'group'    => 'general',
            'label'    => esc_html__('Preview image height', 'bricks'),
            'type'     => 'number',
            'units' => true,
            'description' => esc_html__('The height of the preview images.', 'bricks'),
            'css' => [
                [
                    'property' => 'height',
                    'selector' => '.brf-field-image-preview-wrapper div, .brf-field-image-preview-wrapper img'
                ]
            ]
        ];

        // Preview Image Object Fit
        $this->controls['fileUploadPreviewObjectFit'] = [
            'group'    => 'general',
            'label'    => esc_html__('Preview image object fit', 'bricks'),
            'type'     => 'select',
            'default'  => 'cover',
            'options'  => [
                'cover' => esc_html__('Cover', 'bricks'),
                'contain' => esc_html__('Contain', 'bricks'),
                'fill' => esc_html__('Fill', 'bricks'),
                'none' => esc_html__('None', 'bricks'),
                'scale-down' => esc_html__('Scale Down', 'bricks'),
            ],
            'description' => esc_html__('The object fit of the preview images.', 'bricks'),
            'css' => [
                [
                    'property' => 'object-fit',
                    'selector' => '.brf-field-image-preview-wrapper div, .brf-field-image-preview-wrapper img'
                ]
            ]
        ];

        // @since 1.4 (File upload button style here)
        $this->controls['fileUploadTypography']   = [
            'group'    => 'general',
            'tab'      => 'content',
            'label'    => esc_html__('Typography', 'bricks'),
            'type'     => 'typography',
            'css'      => [
                [
                    'property' => 'font',
                    'selector' => '.choose-files',
                ],
            ],
        ];

        $this->controls['fileUploadBackground']   = [
            'group'    => 'general',
            'tab'      => 'content',
            'label'    => esc_html__('Background', 'bricks'),
            'type'     => 'color',
            'css'      => [
                [
                    'property' => 'background-color',
                    'selector' => '.choose-files',
                ],
            ],
        ];

        $this->controls['fileUploadBorder']       = [
            'group'    => 'general',
            'tab'      => 'content',
            'label'    => esc_html__('Border', 'bricks'),
            'type'     => 'border',
            'css'      => [
                [
                    'property' => 'border',
                    'selector' => '.choose-files',
                ],
            ],
        ];

        $this->controls = array_merge($this->controls, Helper::get_condition_controls());
        $this->controls = array_merge($this->controls, Helper::get_advanced_controls());
    }

    public function render()
    {
        $settings = $this->settings;
        $parent_settings = Helper::get_nestable_parent_settings($this->element) ? Helper::get_nestable_parent_settings($this->element) : [];

        $id = $this->id ? $this->id : false;

        if (isset($settings['id']) && $settings['id']) {
            $id = $settings['id'];
        }

        $random_id = Helpers::generate_random_id(false);
        $label = isset($settings['label']) ? $settings['label'] : false;

        $show_labels = true;
        if (isset($parent_settings) && !empty($parent_settings) && !isset($parent_settings['showLabels'])) {
            $show_labels = false;
        }

        // Single Show Label
        if (isset($settings['showLabel']) && $settings['showLabel']) {
            $show_labels = true;
        }

        $value = isset($settings['value']) ? bricks_render_dynamic_data($settings['value']) : '';
        $required = isset($settings['required']) ? $settings['required'] : false;

        if (!$id && bricks_is_builder()) {
            return $this->render_element_placeholder(
                [
                    'title' => esc_html__('You have to set an ID for your element.', 'bricksforge'),
                ]
            );
        }

        /**
         * Wrapper
         */
        $this->set_attribute('_root', 'class', 'pro-forms-builder-field');
        $this->set_attribute('_root', 'class', 'form-group');
        $this->set_attribute('_root', 'data-element-id', $this->id);

        if (isset($settings['hideImagePreview']) && $settings['hideImagePreview'] === true) {
            $this->set_attribute("field", 'data-hide-image-preview', 'true');
        }

        if ($id !== $this->id) {
            $this->set_attribute('_root', 'data-custom-id', $id);
        }

        // Post Context
        if (isset($settings['postContext'])) {
            $this->set_attribute('_root', 'data-context', bricks_render_dynamic_data($settings['postContext']));
        }

        // Custom Css Class
        if (isset($settings['cssClass']) && $settings['cssClass']) {
            $this->set_attribute('field', 'class', $settings['cssClass']);
        }

        /**
         * Field
         */
        $this->set_attribute('field', 'type', 'file');
        $this->set_attribute('field', 'id', 'form-field-' . $id);
        $this->set_attribute('field', 'name', 'form-field-' . $id . '[]');
        $this->set_attribute('field', 'data-label', $label);

        if (!isset($settings['fileUploadLimit']) || $settings['fileUploadLimit'] > 1) {
            $this->set_attribute("field", 'multiple');
        }

        if (!empty($settings['fileUploadLimit'])) {
            $this->set_attribute("field", 'data-limit', $settings['fileUploadLimit']);
        }

        if (isset($settings['fileUploadReplace']) && $settings['fileUploadReplace'] == true) {
            $this->set_attribute("field", 'data-replace', 'true');
        }

        if (isset($settings['fileUploadDisableDeletion']) && $settings['fileUploadDisableDeletion'] == true) {
            $this->set_attribute("field", 'data-disable-deletion', 'true');
        }

        if (isset($settings['fileUploadAllowedTypes'])) {
            $types = str_replace('.', '', strtolower($settings['fileUploadAllowedTypes']));
            $types = array_map('trim', explode(',', $types));

            if (in_array('jpg', $types) && !in_array('jpeg', $types)) {
                $types[] = 'jpeg';
            }

            array_walk(
                $types,
                function (&$value) {
                    $value = '.' . $value;
                }
            );

            $this->set_attribute("field", 'accept', implode(',', $types));
        }

        if (!empty($settings['fileUploadSize'])) {
            $this->set_attribute("field", 'data-maxsize', $settings['fileUploadSize']);
        }

        // Link the input file to the file preview using a unique ID (the field ID could be duplicated)
        $this->set_attribute("field", 'data-files-ref', $id);

        $this->set_attribute("file-preview", 'data-files-ref', $id);

        $button_text = isset($settings['fileUploadButtonText']) ? $settings['fileUploadButtonText'] : esc_html__('Choose files', 'bricks');

        $this->set_attribute("file-preview", 'class', 'file-result');
        $this->set_attribute("file-preview", 'data-error-limit', esc_html__('File %s not accepted. File limit exceeded.', 'bricks'));
        $this->set_attribute("file-preview", 'data-error-size', esc_html__('File %s not accepted. Size limit exceeded.', 'bricks'));

        $this->set_attribute("label", 'class', 'choose-files');
        $this->set_attribute("label", 'for', 'form-field-' . $id);

        if ($value) {
            $this->set_attribute('field', 'value', $value);
        }

        if ($required) {
            $this->set_attribute('field', 'required');
        }

        // Conditions
        if (isset($settings['hasConditions']) && isset($settings['conditions']) && $settings['conditions']) {
            $this->set_attribute('_root', 'data-brf-conditions', json_encode($settings['conditions']));
        }
        if (isset($settings['conditionsRelation']) && $settings['conditionsRelation']) {
            $this->set_attribute('_root', 'data-brf-conditions-relation', $settings['conditionsRelation']);
        }

        // Required Asterisk
        if (isset($parent_settings['requiredAsterisk']) && $parent_settings['requiredAsterisk'] == true && $required) {
            $this->set_attribute("label", 'class', 'required');
        }

?>
        <div <?php echo $this->render_attributes('_root'); ?>>

            <?php if ($label && $show_labels) : ?>
                <label for="form-field-<?php echo $random_id; ?>"><?php echo $label; ?></label>
            <?php endif; ?>

            <?php if (isset($settings['hideFileNamePreview']) && $settings['hideFileNamePreview'] == true) { ?>
                <?php $this->set_attribute("file-preview", 'class', 'brf-hidden'); ?>
            <?php } ?>

            <div <?php echo $this->render_attributes("file-preview"); ?>>
                <span class="text"></span>
                <button type="button" class="bricks-button remove">
                    <?php esc_html_e('Remove', 'bricks'); ?>
                </button>
            </div>

            <label <?php echo $this->render_attributes("label"); ?>><?php echo $button_text; ?></label>

            <input <?php echo $this->render_attributes('field'); ?>>

            <?php

            $file_init_value = bricks_render_dynamic_data(bricks_render_dynamic_data($value));

            if ($file_init_value && !empty($file_init_value) && $file_init_value != "") {
                echo '<div class="brf-field-image-preview-wrapper">';
                echo '<div class="brf-field-image-preview-3803489059">';
                if (!isset($settings['hideImagePreview']) || $settings['hideImagePreview'] === false) {
                    echo '<img src="' . $file_init_value . '" alt="Image Preview" />';
                    /* echo '<button type="button" class="bricks-button remove">';
        echo esc_html_e('Remove', 'bricks');
        echo '</button>'; */
                }
                echo '</div>';
                echo '</div>';
            }
            ?>

        </div>
<?php
    }
}

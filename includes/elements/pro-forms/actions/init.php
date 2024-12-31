<?php

namespace Bricksforge\ProForms\Actions;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

include_once(BRICKSFORGE_PATH . '/includes/api/FormsHelper.php');

class Init
{
    private $forms_helper;

    public function __construct()
    {
        $this->forms_helper = new \Bricksforge\Api\FormsHelper();
    }

    /**
     * Before Form Submit
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function form_submit(\WP_REST_Request $request)
    {
        $form_data = $request->get_body_params();
        $form_files = $this->handle_files($request->get_file_params());

        $post_id = absint($form_data['postId']);
        $dynamic_post_id = $request->get_param('dynamicPostId');
        $form_id = $request->get_param('formId');
        $field_ids = $request->get_param('fieldIds') ? json_decode($request->get_param('fieldIds')) : null;
        $post_context = $request->get_param('postContext') ? json_decode($request->get_param('postContext')) : null;
        $captcha_result = $request->get_param('captchaResult');
        $turnstile_result = $request->get_param('turnstileResult');

        $form_settings = \Bricks\Helpers::get_element_settings($post_id, $form_id);

        $hcaptcha_enabled = isset($form_settings['enableHCaptcha']) ? $form_settings['enableHCaptcha'] : false;
        $turnstile_enabled = isset($form_settings['enableTurnstile']) ? $form_settings['enableTurnstile'] : false;

        if (!isset($form_settings) || empty($form_settings)) {
            return false;
        }

        if (!isset($form_settings['actions']) || empty($form_settings['actions'])) {
            wp_send_json_error(array(
                'message' => __('No action has been set for this form.', 'bricksforge'),
            ));
        }

        $form_actions = $form_settings['actions'];
        $return_values = array();

        // Honeypot check. If $form_data contains a key form-field-guardian42 and the value is 1, we stop here.
        if (isset($form_data['form-field-guardian42']) && $form_data['form-field-guardian42'] == 1) {
            wp_send_json_error(array(
                'message' => __('You are not allowed to submit this form.', 'bricksforge'),
            ));

            die();
        }

        // First of all, we need to check if the captcha is valid. If not, we need to stop here.
        if ($hcaptcha_enabled == true) {
            if (!$this->forms_helper->handle_hcaptcha($form_settings, $form_data, $captcha_result ? $captcha_result : null)) {
                return false;
            }
        }

        if ($turnstile_enabled == true) {
            if (!$this->forms_helper->handle_turnstile($form_settings, $form_data, $turnstile_result ? $turnstile_result : null)) {
                wp_send_json_error(array(
                    'message' => __(isset($form_settings['turnstileErrorMessage']) ? $form_settings['turnstileErrorMessage'] : 'Your submission is being verified. Please wait a moment before submitting again.', 'bricksforge'),
                ));

                return false;
            }
        }

        // Add File Data to Form Data
        if (isset($form_files) && !empty($form_files)) {
            foreach ($form_files as $field => $files) {
                foreach ($files as $file) {

                    if ($field == 'brfr' && isset($file['fieldId']) && isset($file['itemIndex']) && isset($file['subFieldId'])) {
                        $form_data["brfr"][$file['fieldId']][$file['itemIndex']][$file['subFieldId']] = $file;
                    } else {
                        $form_data[$field] = $file;
                    }
                }
            }
        }

        // Run initial sanitation
        $form_data = $this->forms_helper->initial_sanitization($form_settings, $form_data, $field_ids, $post_id);

        // Validate Fields
        $hidden_fields = $request->get_param('hiddenFields') ? json_decode($request->get_param('hiddenFields')) : null;
        $fields_to_validate = $request->get_param('fieldsToValidate') ? json_decode($request->get_param('fieldsToValidate')) : null;
        $validation_result = $this->forms_helper->validate($field_ids, $form_data, $post_id, $hidden_fields, $fields_to_validate);

        $validation_result = apply_filters('bricksforge/pro_forms/validate', $validation_result, $form_data, $post_id, $form_id);

        if ($validation_result !== true) {
            wp_send_json_error(array(
                'validation' => $validation_result
            ));
        }

        // Trigger bricksforge/pro_forms/before_submit action
        do_action('bricksforge/pro_forms/before_submit', $form_data);

        $form_structure = $this->forms_helper->build_form_structure($post_id, $form_id);

        // Form Base
        $base = new \Bricksforge\ProForms\Actions\Base($form_settings, $form_data, $form_files, $post_id, $form_id, $dynamic_post_id, $form_structure, $post_context);

        // Handle Form Actions
        if (in_array('create_pdf', $form_actions)) {
            $base->update_proceeding_action('create_pdf');

            $action = new \Bricksforge\ProForms\Actions\Create_Pdf();
            $result = $action->run($base);
        }

        if (in_array('registration', $form_actions)) {
            $base->update_proceeding_action('registration');

            $action = new \Bricksforge\ProForms\Actions\Registration();
            $action->run($base);
        }

        if (in_array('post_create', $form_actions)) {
            $base->update_proceeding_action('post_create');

            $action = new \Bricksforge\ProForms\Actions\Create_Post();
            $result = $action->run($base);
        }

        if (in_array('post_update', $form_actions)) {
            $base->update_proceeding_action('post_update');

            $action = new \Bricksforge\ProForms\Actions\Update_Post();
            $result = $action->run($base);
        }

        if (in_array('post_delete', $form_actions)) {
            $base->update_proceeding_action('post_delete');

            $action = new \Bricksforge\ProForms\Actions\Delete_Post();
            $result = $action->run($base);

            if ($result === false) {
                wp_send_json_error(array(
                    'message' => __('Deletion failed.', 'bricksforge'),
                ));
            }
        }

        if (in_array('add_option', $form_actions)) {
            $base->update_proceeding_action('add_option');

            $action = new \Bricksforge\ProForms\Actions\Add_Option();
            $result = $action->run($base);
        }

        if (in_array('update_option', $form_actions)) {
            $base->update_proceeding_action('update_option');

            $action = new \Bricksforge\ProForms\Actions\Update_Option();
            $result = $action->run($base);

            if ($result) {
                $return_values['update_option'] = $result;
            }
        }

        if (in_array('delete_option', $form_actions)) {
            $base->update_proceeding_action('delete_option');

            $action = new \Bricksforge\ProForms\Actions\Delete_Option();
            $result = $action->run($base);
        }

        if (in_array('update_post_meta', $form_actions)) {
            $base->update_proceeding_action('update_post_meta');

            $action = new \Bricksforge\ProForms\Actions\Update_Post_Meta();
            $result = $action->run($base);

            if ($result) {
                $return_values['update_post_meta'] = $result;
            }
        }

        if (in_array('set_storage_item', $form_actions)) {
            $base->update_proceeding_action('set_storage_item');

            $action = new \Bricksforge\ProForms\Actions\Set_Storage_Item();
            $result = $action->run($base);

            if ($result) {
                $return_values['set_storage_item'] = $result;
            }
        }

        if (in_array('create_submission', $form_actions)) {
            $base->update_proceeding_action('create_submission');

            $action = new \Bricksforge\ProForms\Actions\Create_Submission();
            $result = $action->run($base);

            // If result is an array and result[0] is true, we have a duplicate submission
            if (is_array($result) && isset($result["status"]) && $result["status"] === "duplicate") {
                wp_send_json_error(array(
                    'message' => $result["message"],
                ));
            }
        }

        if (in_array('wc_add_to_cart', $form_actions)) {
            $base->update_proceeding_action('wc_add_to_cart');

            $action = new \Bricksforge\ProForms\Actions\Wc_Add_To_Cart();
            $result = $action->run($base);

            if ($result) {
                $return_values['wc_add_to_cart'] = $result;
            }
        }

        if (in_array('webhook', $form_actions)) {
            $base->update_proceeding_action('webhook');

            $action = new \Bricksforge\ProForms\Actions\Webhook();
            $result = $action->run($base);

            if ($result) {
                $return_values['webhook'] = $result;
            }
        }

        if (in_array('login', $form_actions)) {
            $base->update_proceeding_action('login');

            $action = new \Bricksforge\ProForms\Actions\Login();
            $action->run($base);
        }

        if (in_array('update_user_meta', $form_actions)) {
            $base->update_proceeding_action('update_user_meta');

            $action = new \Bricksforge\ProForms\Actions\Update_User_Meta();
            $result = $action->run($base);

            if ($result) {
                $return_values['update_user_meta'] = $result;
            }
        }

        if (in_array('reset_user_password', $form_actions)) {
            $base->update_proceeding_action('reset_user_password');

            $action = new \Bricksforge\ProForms\Actions\Reset_User_Password();
            $action->run($base);
        }

        if (in_array('mailchimp', $form_actions)) {
            $base->update_proceeding_action('mailchimp');

            $action = new \Bricksforge\ProForms\Actions\Mailchimp();
            $action->run($base);
        }

        if (in_array('sendgrid', $form_actions)) {
            $base->update_proceeding_action('sendgrid');

            $action = new \Bricksforge\ProForms\Actions\Sendgrid();
            $action->run($base);
        }

        if (in_array('email', $form_actions)) {
            $base->update_proceeding_action('email');

            $action = new \Bricksforge\ProForms\Actions\Email();
            $action->run($base);
        }

        if (in_array('custom', $form_actions)) {
            $base->update_proceeding_action('custom');

            $action = new \Bricksforge\ProForms\Actions\Custom();
            $action->run($base);
        }

        if (in_array('show_download_info', $form_actions)) {
            $base->update_proceeding_action('show_download_info');

            $download_info_element = array_filter($form_structure, function ($element) {
                return $element['name'] === 'file-download';
            });

            // Re-index the array
            $download_info_element = array_values($download_info_element);

            $download_url = isset($download_info_element) && isset($download_info_element[0]) && isset($download_info_element[0]['settings']) && isset($download_info_element[0]['settings']['download_url']) ? $download_info_element[0]['settings']['download_url'] : '';
            $download_trigger = isset($download_info_element) && isset($download_info_element[0]) && isset($download_info_element[0]['settings']) && isset($download_info_element[0]['settings']['trigger']) ? $download_info_element[0]['settings']['trigger'] : 'automatic';

            $base->set_result(
                array(
                    'action'  => 'show_download_info',
                    'status' => 'success',
                    'trigger' => $download_trigger,
                    'downloadUrl' => $download_url
                )
            );
        }

        if (in_array('redirect', $form_actions)) {
            $base->update_proceeding_action('redirect');

            $action = new \Bricksforge\ProForms\Actions\Redirect();
            $action->run($base);
        }

        if (in_array('reload', $form_actions)) {
            $base->update_proceeding_action('reload');

            $action = new \Bricksforge\ProForms\Actions\Reload();
            $action->run($base);
        }

        if (in_array('confetti', $form_actions)) {
            $base->update_proceeding_action('confetti');

            $base->set_result(
                array(
                    'action'  => 'confetti',
                    'status' => 'success',
                )
            );
        }

        $return_values['results'] = $base->results;

        // Trigger bricksforge/pro_forms/before_submit action
        do_action('bricksforge/pro_forms/after_submit', $form_data, $return_values);

        return $return_values;
    }

    public function handle_files($files)
    {
        require_once(ABSPATH . 'wp-admin/includes/file.php');

        if (empty($files)) {
            return;
        }

        $uploaded_files = [];
        $processed_files = []; // Array to keep track of processed files

        foreach ($files as $input_name => $file_group) {
            $is_repeater = false;

            // Check if comes from repeater field
            if ($input_name == "brfr") {
                $is_repeater = true;
            }

            if ($is_repeater) {

                // Iterate through each level of the repeater field
                foreach ($file_group['name'] as $group_key => $item_group) {
                    foreach ($item_group as $item_key => $sub_item_group) {
                        foreach ($sub_item_group as $sub_item_key => $file_names) {
                            $file_array = $this->prepare_repeater_file_array($group_key, $item_key, $sub_item_key, $file_group);
                            // Use the adjusted structure to process files
                            $this->process_file_group($file_array, $input_name, $uploaded_files, $processed_files);
                        }
                    }
                }
            } else {
                $this->process_file_group($file_group, $input_name, $uploaded_files, $processed_files);
            }
        }

        return $uploaded_files;
    }

    private function process_file_group($file_group, $input_name, &$uploaded_files, &$processed_files)
    {
        if (empty($file_group['name'])) {
            return;
        }

        foreach ($file_group['name'] as $key => $value) {
            if (empty($file_group['name'][$key]) || $file_group['error'][$key] !== UPLOAD_ERR_OK) {
                continue;
            }

            $file_name = $file_group['name'][$key];
            $file_size = $file_group['size'][$key];
            $file_identifier = $file_name . '_' . $file_size; // Create a unique identifier

            $field_id = isset($file_group['fieldId']) ? $file_group['fieldId'] : '';
            $sub_field_id = isset($file_group['subFieldId']) ? $file_group['subFieldId'] : '';
            $item_index = isset($file_group['itemIndex']) ? $file_group['itemIndex'] : false;

            // Check if this file identifier has already been processed
            if (in_array($file_identifier, $processed_files)) {
                continue; // Skip this file as it's a duplicate
            }

            $file = [
                'name'     => $file_name,
                'type'     => $file_group['type'][$key],
                'tmp_name' => $file_group['tmp_name'][$key],
                'error'    => $file_group['error'][$key],
                'size'     => $file_size,
            ];

            if ($field_id) {
                $file['fieldId'] = $field_id;
            }

            if ($sub_field_id) {
                $file['subFieldId'] = $sub_field_id;
            }

            if ($item_index !== false) {
                $file['itemIndex'] = $item_index;
            }

            $uploaded = wp_handle_upload($file, ['test_form' => false]);

            if ($uploaded && !isset($uploaded['error'])) {
                $uploaded['type'] = $file['type'];
                $uploaded['name'] = $file['name'];
                $uploaded['field'] = $input_name;

                if ($field_id) {
                    $uploaded['fieldId'] = $field_id;
                }

                if ($sub_field_id) {
                    $uploaded['subFieldId'] = $sub_field_id;
                }

                if ($item_index !== false) {
                    $uploaded['itemIndex'] = $item_index;
                }

                $uploaded_files[$input_name][] = $uploaded;
                $processed_files[] = $file_identifier; // Add file identifier to processed files
            }
        }
    }

    private function prepare_repeater_file_array($group_key, $item_key, $sub_item_key, $file_group)
    {


        // Extracting and preparing file information from the complex repeater structure
        $file_array = [
            'name' => [],
            'type' => [],
            'tmp_name' => [],
            'error' => [],
            'size' => [],
            'fieldId' => '',
            'itemIndex' => '',
            'subFieldId' => ''
        ];
        foreach (['name', 'type', 'tmp_name', 'error', 'size'] as $attr) {
            foreach ($file_group[$attr][$group_key][$item_key][$sub_item_key] as $index => $value) {
                $file_array[$attr][] = $value;
            }
        }

        // We add the ID to the file array ($group_key)
        $file_array['fieldId'] = $group_key;
        $file_array['subFieldId'] = $sub_item_key;
        $file_array['itemIndex'] = $item_key;

        $result = $file_array;

        return $result;
    }
}

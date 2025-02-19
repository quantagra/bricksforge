<?php

namespace Bricksforge\ProForms\Actions;

class Login
{
    public $name = "login";
    /**
     * User login
     *
     * @since 1.0
     */
    public function run($form)
    {

        $form_settings = $form->get_settings();
        $form_fields   = $form->get_fields();

        $user_login    = isset($form_settings['loginName']) ? $form->get_form_field_by_id($form_settings['loginName']) : false;
        $user_password = isset($form_settings['loginPassword']) ? $form->get_form_field_by_id($form_settings['loginPassword']) : false;

        // Login response: WP_User on success, WP_Error on failure
        $login_response = wp_signon(
            [
                'user_login'    => $user_login,
                'user_password' => $user_password,
                'remember'      => false,
            ]
        );

        if (is_wp_error($login_response)) {
            // Login error
            $form->set_result(
                [
                    'action'  => $this->name,
                    'type'    => 'error',
                    'message' => $login_response->get_error_message(),
                ]
            );

            return;
        }

        $user_id = $login_response->ID;
        if ($user_id) {
            $form->update_live_user_id($user_id);
        }

        $form->set_result(
            [
                'action'         => $this->name,
                'type'           => 'success',
                'login_response' => $login_response,
            ]
        );
    }
}

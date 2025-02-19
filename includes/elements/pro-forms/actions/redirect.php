<?php

namespace Bricksforge\ProForms\Actions;

class Redirect
{
    public $name = "redirect";
    /**
     * User login
     *
     * @since 1.0
     */
    public function run($form)
    {

        $form_settings = $form->get_settings();
        $form_fields   = $form->get_fields();

        $redirect_to = false;

        if (isset($form_settings['redirect'])) {
            $redirect_to = $form->get_form_field_by_id($form_settings['redirect']);
        }

        // Redirect to admin area
        if (isset($form_settings['redirectAdminUrl'])) {
            $redirect_to = isset($form_settings['redirect']) ? admin_url($form_settings['redirect']) : admin_url();

            if (is_multisite()) {
                $user_sites = get_blogs_of_user($login_response->ID);

                foreach ($user_sites as $site_id => $site) {
                    // Skip main site
                    if ($site_id !== 1) {
                        $redirect_to = isset($form_settings['redirect']) ? get_admin_url($site_id, $form_settings['redirect']) : get_admin_url($site_id);
                    }
                }
            }
        }

        if ($redirect_to) {
            $form->set_result(
                [
                    'action'          => $this->name,
                    'type'            => 'redirect',
                    'redirectTo'      => $redirect_to,
                    'redirectTimeout' => isset($form_settings['redirectTimeout']) ? intval($form_settings['redirectTimeout']) : 0
                ]
            );
        }
    }
}

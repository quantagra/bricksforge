<?php
// Show all errors

namespace Bricksforge;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Frontend Pages Handler
 */
class Frontend
{

    private $load_instances = true;
    private $load_timelines = true;
    protected $panel_data = null;

    public function __construct()
    {
        add_action('wp', [$this, 'render_conditionals']);

        if (bricks_is_builder()) {
            wp_enqueue_style('bricksforge-builder');
            wp_enqueue_script('bricksforge-builder');

            if (!bricks_is_builder_iframe()) {
                wp_enqueue_script('bricksforge-builder-scripts');
            }
        }

        $this->check_for_ajax_functions();
        $this->handle_third_party_plugins();

        add_shortcode('bricksforge', [$this, 'render_frontend']);
    }

    public function has_elements_or_options_activated()
    {
        $tools = get_option('brf_activated_tools');
        $elements = get_option('brf_activated_elements');

        // If there are no activated tools or elements (not isset or empty), return false
        if ((!isset($tools) || empty($tools)) && (!isset($elements) || empty($elements))) {
            return false;
        }

        return true;
    }

    public function stylesheet_needed()
    {
        return true;

        // Todo: Check if the page has any of the following elements or options activated
        if (!class_exists('Bricksforge\Helper\ElementsHelper')) {
            return false;
        }

        $tools = get_option('brf_activated_tools');

        if (!empty($tools) && is_array($tools)) {
            // Popups
            if (in_array(5, $tools)) {
                return true;
            }

            // Mega Menu
            if (in_array(3, $tools)) {
                return true;
            }

            // Page Transitions
            if (in_array(16, $tools)) {
                return true;
            }
        }

        $page_data = Helper\ElementsHelper::$page_data_string;

        if (empty($page_data)) {
            return false;
        }

        $allowed_snippets = ['brf-flip', 'brf-before-and-after', 'brf-pro-forms', 'brf-tour'];

        $pattern = '/' . implode('|', array_map('preg_quote', $allowed_snippets)) . '/';

        return preg_match($pattern, $page_data) === 1;
    }

    public function check_for_ajax_functions()
    {
        // Extract data from render_conditionals function
        $panel_data = get_option('brf_panel');
        if ($panel_data) {
            $panel_data = $panel_data[0];
            $instances = $panel_data->instances ?? false;

            if ($instances) {
                $ajax_functions = [];

                foreach ($instances as $instance) {
                    if (isset($instance->disabled) && $instance->disabled) continue;

                    foreach ($instance->actions as $action) {
                        if (isset($action->action->value) && $action->action->value == 'ajaxFunction') {
                            array_push($ajax_functions, $action);
                        }
                    }
                }

                if (count($ajax_functions) > 0) {
                    $this->handle_ajax_functions($ajax_functions);
                }
            }
        }
    }

    public function render_conditionals()
    {
        if ($this->has_elements_or_options_activated() && $this->stylesheet_needed()) {
            wp_enqueue_style('bricksforge-style');
        }

        // Panel
        if (get_option('brf_activated_tools') && in_array(6, get_option('brf_activated_tools'))) {
            $panel_data = get_option('brf_panel');

            if (bricks_is_builder()) {
                wp_enqueue_script('bricksforge-panel');
                wp_enqueue_script('bricksforge-gsap-draggable');
                wp_enqueue_script('bricksforge-gsap-splittext');
                wp_enqueue_script('bricksforge-gsap-flip');
                wp_enqueue_script('bricksforge-gsap-drawsvg');
            }

            if ($panel_data) {
                $panel_data = $panel_data[0];

                $instances = $panel_data->instances ?? false;
                $timelines = $panel_data->timelines ?? false;

                if ($timelines) {
                    $has_enabled_timelines = false;
                    foreach ($timelines as $timeline) {
                        if (isset($timeline->disabled) && $timeline->disabled === false) {
                            $has_enabled_timelines = true;
                            break;
                        }
                    }

                    if ($has_enabled_timelines) {
                        $load_timelines = [];

                        foreach ($timelines as $timeline) {

                            // Check if it needs to be loaded on this page
                            $timeline_needs_loading_check = isset($timeline->loadOnChoice) && $timeline->loadOnChoice == 'specificPages';
                            $timeline_load_on = isset($timeline->loadOn) ? $timeline->loadOn : '';

                            if ($timeline_needs_loading_check && $timeline_load_on == '') {
                                $load_timelines[] = false;
                                continue;
                            }

                            $timeline_post_ids = explode(',', $timeline_load_on);

                            if (is_array($timeline_load_on)) {
                                $timeline_post_ids = array_map('trim', $timeline_load_on);
                            }

                            $timeline_post_ids = array_map(function ($id) {
                                return intval($id);
                            }, $timeline_post_ids);

                            if ($timeline_needs_loading_check == true && !in_array(get_the_ID(), $timeline_post_ids)) {
                                $load_timelines[] = false;
                                continue;
                            } else {
                                $load_timelines[] = true;
                            }
                        }

                        if (!in_array(true, $load_timelines)) {
                            $this->load_timelines = false;
                        }

                        if ($this->load_timelines === true) {
                            wp_enqueue_script('bricksforge-panel');
                            wp_enqueue_script('bricksforge-gsap');

                            $has_scrollTrigger = array_search('scrollTrigger', array_column($timelines, 'trigger')) !== false;
                            $has_drawSVG = strpos(json_encode($timelines), 'drawSVG') !== false;
                            $has_scrollTo = strpos(json_encode($timelines), 'scrollTo') !== false;

                            foreach ($timelines as $timeline) {
                                $has_splitText = array_search('true', array_column($timeline->animations, 'splitText')) !== false;
                                if ($has_splitText) {
                                    break;
                                }
                            }

                            if ($has_scrollTrigger) wp_enqueue_script('bricksforge-gsap-scrolltrigger');
                            if ($has_scrollTo) wp_enqueue_script('bricksforge-gsap-scrollto-plugin');
                            if ($has_splitText) wp_enqueue_script('bricksforge-gsap-splittext');
                            if ($has_drawSVG) wp_enqueue_script('bricksforge-gsap-drawsvg');
                        }
                    }
                }

                if ($instances) {

                    $has_gsapFlip = $has_gsapSet = $has_gsapTo = $has_draw_svg = $has_gsap = $has_confetti = false;

                    $ajax_functions = [];

                    $load_instances = [];

                    foreach ($instances as $instance) {
                        if (isset($instance->disabled) && $instance->disabled) continue;

                        // Check if it needs to be loaded on this page
                        $instance_needs_loading_check = isset($instance->loadOnChoice) && $instance->loadOnChoice == 'specificPages';
                        $instance_load_on = isset($instance->loadOn) ? $instance->loadOn : '';

                        if ($instance_needs_loading_check && $instance_load_on == '') {
                            $load_instances[] = false;
                            continue;
                        }

                        $instance_post_ids = explode(',', $instance_load_on);

                        if (is_array($instance_load_on)) {
                            $instance_post_ids = array_map('trim', $instance_post_ids);
                        }

                        $instance_post_ids = array_map(function ($id) {
                            return intval($id);
                        }, $instance_post_ids);

                        if ($instance_needs_loading_check == true && !in_array(get_the_ID(), $instance_post_ids)) {
                            $load_instances[] = false;
                            continue;
                        } else {
                            $load_instances[] = true;
                        }

                        foreach ($instance->actions as $action) {
                            $has_gsapFlip = $has_gsapFlip || (isset($action->action->value) && $action->action->value == 'gsapFlip');
                            $has_gsapSet = $has_gsapSet || (isset($action->action->value) && $action->action->value == 'gsapSet');
                            $has_gsapTo = $has_gsapTo || (isset($action->action->value) && $action->action->value == 'gsapTo');
                            $has_draw_svg = $has_draw_svg || (isset($action->action->gsapSetObject) && strpos($action->action->gsapSetObject, 'drawSVG') !== false);
                            $has_gsap = $has_gsap || (isset($action->action->value) && $action->action->value == 'gsap');
                            $has_confetti = $has_confetti || (isset($action->action->value) && $action->action->value == 'makeConfetti');

                            $has_scrollTo = strpos(json_encode($action), 'scrollTo') !== false;
                        }
                    }

                    // If $load_instances contains at least one true value, we set the load_instances flag to true
                    if (!in_array(true, $load_instances)) {
                        $this->load_instances = false;
                    }

                    if ($this->load_instances === true) {

                        wp_enqueue_script('bricksforge-panel');

                        if ($has_gsapSet || $has_gsap || $has_gsapTo) {
                            wp_enqueue_script('bricksforge-gsap');

                            if ($has_draw_svg) {
                                wp_enqueue_script('bricksforge-gsap-drawsvg');
                            }

                            if ($has_scrollTo) {
                                wp_enqueue_script('bricksforge-gsap-scrollto-plugin');
                            }
                        }

                        if ($has_gsapFlip) {
                            wp_enqueue_script('bricksforge-gsap-flip');
                        }

                        if ($has_confetti) {
                            wp_enqueue_script('bricksforge-confetti');
                        }
                    }
                }
            }
        }

        if (get_option('brf_activated_tools') && in_array(1, get_option('brf_activated_tools'))) {
            add_action('wp_enqueue_scripts', function () {
                wp_localize_script(
                    'bricksforge-animator',
                    'BRFANIMATIONS',
                    array(
                        'nonce'             => wp_create_nonce('wp_rest'),
                        'siteurl'           => get_option('siteurl'),
                        'pluginurl'         => BRICKSFORGE_URL,
                        'apiurl'            => get_rest_url() . "bricksforge/v1/",
                        'bricksPrefix'      => BRICKSFORGE_BRICKS_ELEMENT_PREFIX,
                    )
                );
            });
        }

        if (get_option('brf_activated_tools') && in_array(5, get_option('brf_activated_tools')) && get_option('brf_popups') && count(get_option('brf_popups')) > 0) {
            wp_enqueue_script('bricksforge-popups');
            add_action('wp_enqueue_scripts', function () {
                wp_localize_script(
                    'bricksforge-popups',
                    'BRFPOPUPS',
                    array(
                        'nonce'       => wp_create_nonce('wp_rest'),
                        'popups'      => get_option('brf_popups'),
                        'apiurl'      => get_rest_url() . "bricksforge/v1/",
                        'currentPage' => get_the_ID(),
                    )
                );
            });
        }

        // Scroll Smoother

        if (get_option('brf_activated_tools') && in_array(7, get_option('brf_activated_tools'))) {

            $scrollsmooth_provider = 'gsap';

            $scrollsmooth_settings = get_option('brf_tool_settings');

            if ($scrollsmooth_settings) {
                // Get the scrollsmooth settings with the key id equal to 7
                $scrollsmooth_settings = array_filter($scrollsmooth_settings, function ($setting) {
                    return $setting->id == 7;
                });

                if ($scrollsmooth_settings) {

                    // Reset the array index
                    $scrollsmooth_settings = array_values($scrollsmooth_settings);

                    $scrollsmooth_settings = $scrollsmooth_settings[0];
                    $scrollsmooth_provider = isset($scrollsmooth_settings->settings->provider) ? $scrollsmooth_settings->settings->provider : 'gsap';
                }
            }

            if (!$scrollsmooth_provider) {
                $scrollsmooth_provider = 'gsap';
            }

            switch ($scrollsmooth_provider) {
                case 'gsap':
                    wp_enqueue_script('bricksforge-gsap-scrollsmoother');

                    // Wrap needed container IDs
                    add_action('bricks_before_site_wrapper', function () {
                        echo '<div id="smooth-wrapper">';
                        echo '<div id="smooth-content">';
                    });
                    add_action('bricks_after_site_wrapper', function () {
                        echo '</div>';
                        echo '</div>';
                    });
                    break;
                case 'lenis':
                    wp_enqueue_script('bricksforge-lenis');
                    break;
                default:
                    break;
            }

            wp_enqueue_script('bricksforge-scrollsmoother');
            add_action('wp_enqueue_scripts', function () {
                $args = array(
                    'toolSettings' => get_option('brf_tool_settings')
                );

                wp_localize_script('bricksforge-scrollsmoother', 'BRFSCROLLSMOOTHER', $args);
            });
        }

        if (bricks_is_builder()) {
            add_action('wp_enqueue_scripts', function () {
                // Builder Scripts
                $args = array(
                    'nonce'                     => wp_create_nonce('wp_rest'),
                    'apiurl'  => get_rest_url() . "bricksforge/v1/",
                );
                wp_localize_script('bricksforge-builder-scripts', 'BRFBUILDER', $args);
            });
        }

        // Bricksforge Terminal
        if (get_option('brf_activated_tools') && in_array(8, get_option('brf_activated_tools')) && bricks_is_builder() && !bricks_is_builder_iframe()) {
            wp_enqueue_script('bricksforge-terminal');

            add_action('wp_enqueue_scripts', function () {
                $args = array(
                    'nonce'   => wp_create_nonce('wp_rest'),
                    'apiurl'  => get_rest_url() . "bricksforge/v1/",
                    'history' => get_option('brf_terminal_history'),
                    'commands' => get_option('brf_terminal_commands') ? get_option('brf_terminal_commands') : [],
                );

                wp_localize_script('bricksforge-terminal', 'BRFTERMINAL', $args);
            });
        }

        // Global Vars
        add_action('wp_enqueue_scripts', function () {
            $args = array(
                'nonce'                     => wp_create_nonce('wp_rest'),
                'ajaxNonce'                =>  wp_create_nonce('bricksforge_ajax'),
                'siteurl'                   => get_option('siteurl'),
                'ajaxurl'                   => admin_url('admin-ajax.php'),
                'postId'                    => get_the_ID(),
                'pluginurl'                 => BRICKSFORGE_URL,
                'apiurl'                    => get_rest_url() . "bricksforge/v1/",
                'brfGlobalClassesActivated' => get_option('brf_global_classes_activated'),
                'brfActivatedTools'         => get_option('brf_activated_tools'),
                'panel'                     => $this->get_panel_data(),
                'panelActivated'            => get_option('brf_activated_tools') && in_array(6, get_option('brf_activated_tools')),
                'aiEnabled'               => get_option('brf_activated_tools') && in_array(14, get_option('brf_activated_tools')),
            );

            if (bricks_is_builder()) {
                $args['permissions'] = get_option('brf_permissions_roles');
                $args['currentUserRole'] = $this->get_current_user_role();
            }

            wp_localize_script('bricksforge-panel', 'BRFPANEL', $args);
        });
    }

    public function get_panel_data()
    {
        $data = get_option('brf_panel');

        if (!bricks_is_frontend()) {
            return $data;
        }

        $stringified_data = json_encode($data);

        // {dynamic:tag}
        preg_match_all('/\{dynamic:([\w:]+)\}/', $stringified_data, $matches);

        if (count($matches) > 0) {
            foreach ($matches[1] as $match) {
                $rendered_data = bricks_render_dynamic_data('{' . $match . '}');

                // If is not a string, we convert it to a string
                if (!is_string($rendered_data)) {
                    $rendered_data = json_encode($rendered_data);
                }

                // Replace double quotes with single quotes to avoid conflicts
                $rendered_data = str_replace('"', "'", $rendered_data);

                $stringified_data = str_replace('{dynamic:' . $match . '}', $rendered_data, $stringified_data);
            }
        }

        $is_valid_json = false;

        if (json_decode($stringified_data) !== null) {
            $is_valid_json = true;
        }

        if (!$is_valid_json) {
            return $data;
        }

        $data = json_decode($stringified_data);

        return $data;
    }

    public function get_current_user_role()
    {
        global $current_user;

        $user_roles = $current_user->roles;
        $user_role = array_shift($user_roles);

        return $user_role;
    }

    public function load_instance($instance)
    {
        return true;
    }

    public function handle_ajax_functions($actions)
    {
        foreach ($actions as $action) {
            $action = $action->action;

            if (!isset($action->ajaxFunctionName)) {
                error_log('Function name is not set');
                continue;
            }

            $function_name = $action->ajaxFunctionName;
            $no_priv = $action->ajaxFunctionNoPriv ?? false;

            if (!isset($function_name) || $function_name == '') {
                error_log('Function name is not set');
                continue;
            }

            if (!function_exists($function_name)) {
                error_log('Function ' . $function_name . ' does not exist');
                continue;
            }

            // Use wordpress ajax actions to handle the ajax request
            add_action('wp_ajax_' . $function_name, $function_name);

            if ($no_priv) {
                add_action('wp_ajax_nopriv_' . $function_name, $function_name);
            }
        }
    }

    public function handle_third_party_plugins()
    {
        // WP Gridbuilder (Todo)
    }

    /**
     * Render frontend app
     *
     * @param  array $atts
     * @param  string $content
     *
     * @return string
     */
    public function render_frontend($atts, $content = '')
    {
        wp_enqueue_style('bricksforge-builder');
        wp_enqueue_style('bricksforge-style');
        wp_enqueue_script('bricksforge-builder');

        $content .= '<div id="bricksforge-triggers"></div>';

        return $content;
    }
}

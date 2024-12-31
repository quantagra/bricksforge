<?php

namespace Bricksforge;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * WhiteLabel Handler
 */
class WhiteLabel
{

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        if ($this->activated() === true) {
            add_action('admin_enqueue_scripts', [$this, 'load_wp_media_files'], 11);
        }
    }

    public function activated()
    {
        return true;
    }

    public function load_wp_media_files()
    {
        wp_enqueue_media();
    }
}

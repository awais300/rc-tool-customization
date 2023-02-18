<?php

namespace EWA\RCTool;

defined('ABSPATH') || exit;

/**
 * Class TemplateLoader
 */

class Helper extends Singleton
{
    /**
     * Check if current user belong to distributor role.
     * @return boolean
     */
    public function is_distributor()
    {
        if (get_current_user_id() == 0) {
            return false;
        }

        $user = wp_get_current_user();
        $allowed_roles = array('distributor', 'administrator');

        if (array_intersect($allowed_roles, $user->roles)) {
            return true;
        } else {
            return false;
        }
    }
}

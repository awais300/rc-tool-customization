<?php

namespace EWA\RCTool;

defined('ABSPATH') || exit;

/**
 * Class TemplateLoader
 * @package EWA\RCTool
 */
class TemplateLoader extends Singleton
{

    /**
     * Loads a template.
     *
     * @param  string $template_name The name of the template to load.
     * @param  array $args           Array of arguments to pass to the template.
     * @param  string $template_path The path to the template file.
     * @param  bool $echo            Whether to echo the template output or return it.
     * @return string|null           Template output if $echo is false, null otherwise.
     * @throws \Exception            If the specified template path does not exist.
     */
    public function get_template($template_name, $args = array(), $template_path, $echo = false)
    {
        $output = null;

        $template_path = $template_path . $template_name;

        if (file_exists($template_path)) {
            extract($args); // @codingStandardsIgnoreLine required for template.

            ob_start();
            include $template_path;
            $output = ob_get_clean();
        } else {
            throw new \Exception(__('Specified path does not exist', 'rct-customization'));
        }

        if ($echo) {
            print $output;
        } else {
            return $output;
        }
    }
}

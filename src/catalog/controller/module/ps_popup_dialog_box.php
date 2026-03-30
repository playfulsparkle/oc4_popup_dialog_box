<?php
namespace Opencart\Catalog\Controller\Extension\PsPopupDialogBox\Module;
/**
 * Class PsPopupDialogBox
 *
 * @package Opencart\Catalog\Controller\Extension\PsPopupDialogBox\Module
 */
class PsPopupDialogBox extends \Opencart\System\Engine\Controller
{
    /**
     * Index
     *
     * @param array<string, mixed> $setting array of module settings
     *
     * @return string
     */
    public function index(array $setting): string
    {
        static $module = 0;

        if ($module === 0) {
            $this->document->addStyle('extension/ps_popup_dialog_box/catalog/view/stylesheet/ps_popup_dialog_box.min.css');

            $this->document->addScript('extension/ps_popup_dialog_box/catalog/view/javascript/ps_popup_dialog_box.min.js');
        }

        $this->load->language('extension/ps_popup_dialog_box/module/ps_popup_dialog_box');

        $language_id = $this->config->get('config_language_id');

        $data['content_url'] = isset($setting['content_url'][$language_id]) ? $setting['content_url'][$language_id] : '';
        $data['content'] = isset($setting['content'][$language_id]) ? html_entity_decode($setting['content'][$language_id], ENT_QUOTES, 'UTF-8') : '';

        if ($data['content_url']) {
            $data['content'] = $this->normalizeContent($data['content']);
        }

        $data['bg_image'] = isset($setting['bg_image'][$language_id]['image']) && $setting['bg_image'][$language_id]['image'] ? $this->config->get('config_url') . 'image/' . html_entity_decode($setting['bg_image'][$language_id]['image'], ENT_QUOTES, 'UTF-8') : '';

        $positionMap = [
            'top_left' => ['position' => 'top-left', 'suffix' => 'TopLeft'],
            'top_center' => ['position' => 'top-center', 'suffix' => 'TopCenter'],
            'top_right' => ['position' => 'top-right', 'suffix' => 'TopRight'],
            'center_left' => ['position' => 'center-left', 'suffix' => 'CenterLeft'],
            'center_center' => ['position' => 'center-center', 'suffix' => 'CenterCenter'],
            'center_right' => ['position' => 'center-right', 'suffix' => 'CenterRight'],
            'bottom_left' => ['position' => 'bottom-left', 'suffix' => 'BottomLeft'],
            'bottom_center' => ['position' => 'bottom-center', 'suffix' => 'BottomCenter'],
            'bottom_right' => ['position' => 'bottom-right', 'suffix' => 'BottomRight'],
        ];

        $pos = isset($positionMap[$setting['position']])
            ? $positionMap[$setting['position']]
            : ['position' => 'center-center', 'suffix' => 'CenterCenter'];

        $data['position'] = $pos['position'];
        $data['animation_in'] = $setting['animation_in'] . $pos['suffix'];
        $data['animation_out'] = $setting['animation_out'] . $pos['suffix'];

        $data['trigger'] = $setting['trigger'];
        $data['close_behavior'] = $this->getCookieDays($setting['close_behavior']);
        $data['page_load_delay'] = $setting['page_load_delay'];
        $data['scroll_threshold'] = $setting['scroll_threshold'];
        $data['close_overlay_click'] = $setting['close_overlay_click'];
        $data['width'] = $setting['width'];
        $data['height'] = $setting['height'];
        $data['bg_color'] = $setting['bg_color'];
        $data['box_shadow_color'] = $this->hexToRgb($setting['box_shadow_color']);
        $data['box_shadow_opacity'] = $setting['box_shadow_opacity'];
        $data['backdrop_color'] = $setting['backdrop_color'];
        $data['backdrop_opacity'] = $setting['backdrop_opacity'];
        $data['border_radius'] = $setting['border_radius'];
        $data['cookie_name'] = $setting['cookie_name'];
        $data['module'] = $module++;

        return $this->load->view('extension/ps_popup_dialog_box/module/ps_popup_dialog_box', $data);
    }

    /**
     * Returns the number of days corresponding to a cookie behavior string.
     *
     * This method maps predefined behavior strings to their respective cookie
     * lifetimes in days. It is used to determine how long a cookie should persist
     * based on user preference or configuration.
     *
     * @param string $behavior The behavior identifier. Acceptable values:
     *                         - 'immediately' → 0 days (session cookie)
     *                         - 'day' → 1 day
     *                         - 'week' → 7 days
     *                         - 'month' → 30 days
     *                         - 'year' → 365 days
     *                         Any other value defaults to 30 days.
     *
     * @return int The number of days the cookie should live.
     */
    private function getCookieDays(string $behavior): int
    {
        switch ($behavior) {
            case 'immediately':
                return 0;
            case 'day':
                return 1;
            case 'week':
                return 7;
            case 'month':
                return 30;
            case 'year':
                return 365;
            default:
                return 30; // fallback
        }
    }

    /**
     * Converts a hexadecimal color code to a comma-separated RGB string.
     *
     * Supports the following formats:
     * - 3-digit: #fff or fff
     * - 6-digit: #ff0000 or ff0000
     * - 8-digit: #ff00007f or ff00007f (alpha channel is ignored)
     *
     * For invalid inputs, it returns "0, 0, 0" as a fallback.
     *
     * @param string $color The hexadecimal color code.
     *
     * @return string The RGB values in the format "r, g, b".
     */
    private function hexToRgb(string $color): string
    {
        // Remove leading '#' if present
        $hex = ltrim($color, '#');

        // Expand 3-digit shorthand to 6-digit
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        // For 8-digit hex, keep only the first 6 characters (ignore alpha)
        if (strlen($hex) === 8) {
            $hex = substr($hex, 0, 6);
        }

        // Validate hex length and characters
        if (strlen($hex) !== 6 || !ctype_xdigit($hex)) {
            // Return a fallback (black) for invalid input
            return '0, 0, 0';
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return "$r, $g, $b";
    }

    /**
     * Normalizes a description string by removing HTML tags and collapsing all
     * whitespace characters (including newlines, tabs, and multiple spaces) into
     * a single space, then trims the result.
     *
     * This method is useful for cleaning user-generated content or input data
     * to obtain a plain, single-line string without any HTML formatting.
     *
     * @param string $description The raw description string to normalize.
     *
     * @return string The normalized string with HTML tags removed, all whitespace
     *                reduced to single spaces, and leading/trailing whitespace trimmed.
     */
    private function normalizeContent(string $description): string
    {
        return trim(
            preg_replace(
                ['/[\r\n\t]+/', '/\s+/i'], // Combine newlines, tabs, and spaces
                [' ', ' '],            // Replace them with single space or empty string
                strip_tags($description) // Strip tags
            )
        );
    }
}

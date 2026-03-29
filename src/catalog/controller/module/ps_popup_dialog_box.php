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

        $data['content'] = isset($setting['content'][$language_id]) ? html_entity_decode($setting['content'][$language_id], ENT_QUOTES, 'UTF-8') : '';
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

    private function hexToRgb(string $hex): string
    {
        // Remove # if present
        $hex = ltrim($hex, '#');

        // If shorthand (e.g., #000) expand to full
        if (strlen($hex) == 3) {
            $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
            $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
            $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        return "$r, $g, $b";
    }
}

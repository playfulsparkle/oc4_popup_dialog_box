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

        $language_id = $this->config->get('config_language_id');

        $data['content'] = isset($setting['content'][$language_id]) ? html_entity_decode($setting['content'][$language_id], ENT_QUOTES, 'UTF-8') : '';
        $data['bg_image'] = isset($setting['bg_image'][$language_id]['image']) ? $this->config->get('config_url') . 'image/' . html_entity_decode($setting['bg_image'][$language_id]['image'], ENT_QUOTES, 'UTF-8') : '';

        switch ($setting['position']) {
            case 'top_left':
                $data['position'] = 'top-left';
                $data['animation_in'] = $setting['animation_in'] . 'TopLeft';
                $data['animation_out'] = $setting['animation_out'] . 'TopLeft';
                break;
            case 'top_center':
                $data['position'] = 'top-center';
                $data['animation_in'] = $setting['animation_in'] . 'TopCenter';
                $data['animation_out'] = $setting['animation_out'] . 'TopCenter';
                break;
            case 'top_right':
                $data['position'] = 'top-right';
                $data['animation_in'] = $setting['animation_in'] . 'TopRight';
                $data['animation_out'] = $setting['animation_out'] . 'TopRight';
                break;
            case 'center_left':
                $data['position'] = 'center-left';
                $data['animation_in'] = $setting['animation_in'] . 'CenterLeft';
                $data['animation_out'] = $setting['animation_out'] . 'CenterLeft';
                break;
            case 'center_right':
                $data['position'] = 'center-right';
                $data['animation_in'] = $setting['animation_in'] . 'CenterRight';
                $data['animation_out'] = $setting['animation_out'] . 'CenterRight';
                break;
            case 'bottom_left':
                $data['position'] = 'bottom-left';
                $data['animation_in'] = $setting['animation_in'] . 'BottomLeft';
                $data['animation_out'] = $setting['animation_out'] . 'BottomLeft';
                break;
            case 'bottom_center':
                $data['position'] = 'bottom-center';
                $data['animation_in'] = $setting['animation_in'] . 'BottomCenter';
                $data['animation_out'] = $setting['animation_out'] . 'BottomCenter';
                break;
            case 'bottom_right':
                $data['position'] = 'bottom-right';
                $data['animation_in'] = $setting['animation_in'] . 'BottomRight';
                $data['animation_out'] = $setting['animation_out'] . 'BottomRight';
                break;
            default:
                // Default to centered if unknown
                $data['position'] = 'center-center';
                $data['animation_in'] = $setting['animation_in'] . 'CenterCenter';
                $data['animation_out'] = $setting['animation_out'] . 'CenterCenter';
                break;
        }

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

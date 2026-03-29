<?php
namespace Opencart\Admin\Controller\Extension\PsPopupDialogBox\Module;
/**
 * Class PsPopupDialogBox
 *
 * @package Opencart\Admin\Controller\Extension\PsPopupDialogBox\Module
 */
class PsPopupDialogBox extends \Opencart\System\Engine\Controller
{
    /**
     * @var string The support email address.
     */
    const EXTENSION_EMAIL = 'support@playfulsparkle.com';

    /**
     * @var string The documentation URL for the extension.
     */
    const EXTENSION_DOC = 'https://github.com/playfulsparkle/oc4_popup_dialog_box.git';

    /**
     * Index
     *
     * @return void
     */
    public function index(): void
    {
        $this->load->language('extension/ps_popup_dialog_box/module/ps_popup_dialog_box');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->document->addScript('view/javascript/ckeditor/ckeditor.js');
        $this->document->addScript('view/javascript/ckeditor/adapters/jquery.js');

        $data['breadcrumbs'] = [];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module')
        ];

        if (!isset($this->request->get['module_id'])) {
            $data['breadcrumbs'][] = [
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/ps_popup_dialog_box/module/ps_popup_dialog_box', 'user_token=' . $this->session->data['user_token'])
            ];
        } else {
            $data['breadcrumbs'][] = [
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/ps_popup_dialog_box/module/ps_popup_dialog_box', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'])
            ];
        }

        $separator = version_compare(VERSION, '4.0.2.0', '>=') ? '.' : '|';

        if (!isset($this->request->get['module_id'])) {
            $data['save'] = $this->url->link('extension/ps_popup_dialog_box/module/ps_popup_dialog_box' . $separator . 'save', 'user_token=' . $this->session->data['user_token']);
        } else {
            $data['save'] = $this->url->link('extension/ps_popup_dialog_box/module/ps_popup_dialog_box' . $separator . 'save', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id']);
        }

        $data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');

        $data['oc4_separator'] = $separator;

        $data['user_token'] = $this->session->data['user_token'];

        $data['text_layout'] = sprintf($this->language->get('text_layout'), $this->url->link('design/layout', 'user_token=' . $this->session->data['user_token']));

        if (isset($this->request->get['module_id'])) {
            $this->load->model('setting/module');

            $module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
        }

        if (isset($module_info['name'])) {
            $data['name'] = $module_info['name'];
        } else {
            $data['name'] = '';
        }

        if (isset($module_info['content'])) {
            $data['content'] = (array) $module_info['content'];
        } else {
            $data['content'] = [];
        }

        if (isset($module_info['cookie_name'])) {
            $data['cookie_name'] = $module_info['cookie_name'];
        } else {
            $data['cookie_name'] = $this->generateUniqueCookieName();
        }

        if (isset($module_info['position'])) {
            $data['position'] = $module_info['position'];
        } else {
            $data['position'] = 'center_center';
        }

        if (isset($module_info['trigger'])) {
            $data['trigger'] = $module_info['trigger'];
        } else {
            $data['trigger'] = 'page_load';
        }

        if (isset($module_info['page_load_delay'])) {
            $data['page_load_delay'] = $module_info['page_load_delay'];
        } else {
            $data['page_load_delay'] = 0;
        }

        if (isset($module_info['scroll_threshold'])) {
            $data['scroll_threshold'] = $module_info['scroll_threshold'];
        } else {
            $data['scroll_threshold'] = 0;
        }

        if (isset($module_info['width'])) {
            $data['width'] = $module_info['width'];
        } else {
            $data['width'] = '';
        }

        if (isset($module_info['height'])) {
            $data['height'] = $module_info['height'];
        } else {
            $data['height'] = '';
        }

        if (isset($module_info['bg_color'])) {
            $data['bg_color'] = $module_info['bg_color'];
        } else {
            $data['bg_color'] = '#ffffff';
        }

        if (isset($module_info['box_shadow_color'])) {
            $data['box_shadow_color'] = $module_info['box_shadow_color'];
        } else {
            $data['box_shadow_color'] = '#000000';
        }

        if (isset($module_info['box_shadow_opacity'])) {
            $data['box_shadow_opacity'] = $module_info['box_shadow_opacity'];
        } else {
            $data['box_shadow_opacity'] = '0.5';
        }

        if (isset($module_info['backdrop_color'])) {
            $data['backdrop_color'] = $module_info['backdrop_color'];
        } else {
            $data['backdrop_color'] = '#000000';
        }

        if (isset($module_info['backdrop_opacity'])) {
            $data['backdrop_opacity'] = $module_info['backdrop_opacity'];
        } else {
            $data['backdrop_opacity'] = '0.5';
        }

        if (isset($module_info['border_radius'])) {
            $data['border_radius'] = $module_info['border_radius'];
        } else {
            $data['border_radius'] = 16;
        }

        if (isset($module_info['close_behavior'])) {
            $data['close_behavior'] = $module_info['close_behavior'];
        } else {
            $data['close_behavior'] = 'immediately';
        }

        if (isset($module_info['animation_in'])) {
            $data['animation_in'] = $module_info['animation_in'];
        } else {
            $data['animation_in'] = 'fadeIn';
        }

        if (isset($module_info['animation_out'])) {
            $data['animation_out'] = $module_info['animation_out'];
        } else {
            $data['animation_out'] = 'fadeOut';
        }

        if (isset($module_info['close_overlay_click'])) {
            $data['close_overlay_click'] = $module_info['close_overlay_click'];
        } else {
            $data['close_overlay_click'] = '1';
        }

        $this->load->model('localisation/language');

        $languages = $this->model_localisation_language->getLanguages();

        $data['languages'] = $languages;

        $this->load->model('tool/image');

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', $this->config->get('config_image_default_width'), $this->config->get('config_image_default_height'));

        $bg_images = [];

        if (isset($module_info['bg_image'])) {
            $bg_images = (array) $module_info['bg_image'];
        } else {
            foreach ($languages as $language) {
                $bg_images[$language['language_id']] = [];
            }
        }

        foreach ($bg_images as $language_id => $bg_image) {
            if (isset($bg_image['image']) && $bg_image['image'] && is_file(DIR_IMAGE . html_entity_decode($bg_image['image'], ENT_QUOTES, 'UTF-8'))) {
                $bg_images[$language_id]['thumb'] = $this->model_tool_image->resize(
                    $bg_image['image'],
                    $this->config->get('config_image_default_width'),
                    $this->config->get('config_image_default_height')
                );
            } else {
                $bg_images[$language_id]['thumb'] = $data['placeholder'];
            }
        }

        $data['bg_image'] = $bg_images;

        if (isset($module_info['status'])) {
            $data['status'] = $module_info['status'];
        } else {
            $data['status'] = '';
        }

        if (isset($this->request->get['module_id'])) {
            $data['module_id'] = (int) $this->request->get['module_id'];
        } else {
            $data['module_id'] = 0;
        }

        $data['positions'] = [
            'top_left' => $this->language->get('text_top_left'),
            'top_center' => $this->language->get('text_top_center'),
            'top_right' => $this->language->get('text_top_right'),

            'center_left' => $this->language->get('text_center_left'),
            'center_center' => $this->language->get('text_center_center'),
            'center_right' => $this->language->get('text_center_right'),

            'bottom_left' => $this->language->get('text_bottom_left'),
            'bottom_center' => $this->language->get('text_bottom_center'),
            'bottom_right' => $this->language->get('text_bottom_right'),
        ];

        $data['triggers'] = [
            'page_load' => $this->language->get('text_page_load'),
            'exit_intent' => $this->language->get('text_exit_intent'),
            'scroll' => $this->language->get('text_scroll'),
        ];

        $data['close_behaviors'] = [
            'immediately' => $this->language->get('text_reappear_immediately'),
            'day' => $this->language->get('text_reappear_day'),
            'week' => $this->language->get('text_reappear_week'),
            'month' => $this->language->get('text_reappear_month'),
            'year' => $this->language->get('text_reappear_year'),
        ];

        $data['animations_in'] = [
            'fadeIn' => $this->language->get('text_animation_fade'),
            'zoomIn' => $this->language->get('text_animation_zoom'),
            'slideIn' => $this->language->get('text_animation_slide'),
        ];

        $data['animations_out'] = [
            'fadeOut' => $this->language->get('text_animation_fade'),
            'zoomOut' => $this->language->get('text_animation_zoom'),
            'slideOut' => $this->language->get('text_animation_slide'),
        ];

        $data['text_contact'] = sprintf($this->language->get('text_contact'), self::EXTENSION_EMAIL, self::EXTENSION_EMAIL, self::EXTENSION_DOC);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/ps_popup_dialog_box/module/ps_popup_dialog_box', $data));
    }

    /**
     * Save
     *
     * @return void
     */
    public function save(): void
    {
        $this->load->language('extension/ps_popup_dialog_box/module/ps_popup_dialog_box');

        $json = [];

        if (!$this->user->hasPermission('modify', 'extension/ps_popup_dialog_box/module/ps_popup_dialog_box')) {
            $json['error']['warning'] = $this->language->get('error_permission');
        }

        $required = [
            'module_id' => 0,
            'name' => '',
            'cookie_name' => '',
            'page_load_delay' => 0,
            'scroll_threshold' => 0,
            'width' => 0,
            'height' => 0
        ];

        $post_info = $this->request->post + $required;

        if ((strlen($post_info['name']) < 3) || (strlen($post_info['name']) > 64)) {
            $json['error']['name'] = $this->language->get('error_name');
        }

        if ((strlen($post_info['cookie_name']) < 3) || (strlen($post_info['cookie_name']) > 64)) {
            $json['error']['cookie_name'] = $this->language->get('error_cookie_name');
        }

        if (strlen($post_info['cookie_name']) < 3 || strlen($post_info['cookie_name']) > 24) { // 1. Length check (3-24 characters)
            $json['error']['cookie_name'] = $this->language->get('error_cookie_name');
        } elseif (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $post_info['cookie_name'])) { // 2. Allowed characters: alphanumeric, underscore, hyphen, dot
            $json['error']['cookie_name'] = $this->language->get('error_cookie_name');
        } elseif (preg_match('/^[0-9]/', $post_info['cookie_name'])) { // 3. Optional: must not start with a digit (recommended for compatibility)
            $json['error']['cookie_name'] = $this->language->get('error_cookie_name');
        } elseif (strpos($post_info['cookie_name'], '__') === 0) { // 4. Optional: prevent reserved prefixes like "__" (used by some browsers)
            $json['error']['cookie_name'] = $this->language->get('error_cookie_name');
        }

        if ($post_info['trigger'] === 'page_load' && $post_info['page_load_delay'] < 0) {
            $json['error']['page-load-delay'] = $this->language->get('error_page_load_delay');
        }

        if ($post_info['trigger'] === 'scroll' && !$post_info['scroll_threshold']) {
            $json['error']['scroll-threshold'] = $this->language->get('error_scroll_threshold');
        }

        if (!$post_info['width']) {
            $json['error']['width'] = $this->language->get('error_width');
        }

        if (!$post_info['height']) {
            $json['error']['height'] = $this->language->get('error_height');
        }

        if (!$json) {
            // Extension
            $this->load->model('setting/module');

            if (!$post_info['module_id']) {
                $json['module_id'] = $this->model_setting_module->addModule('ps_popup_dialog_box.ps_popup_dialog_box', $post_info);
            } else {
                $this->model_setting_module->editModule($post_info['module_id'], $post_info);
            }

            $json['success'] = $this->language->get('text_success');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function install(): void
    {

    }

    public function uninstall(): void
    {

    }

    public function generate_cookie_name(): void
    {
        $json['cookie_name'] = $this->generateUniqueCookieName();

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Generate a unique cookie name that passes the validation rules.
     *
     * Rules:
     * - Length: 3-24 characters
     * - Allowed: a-z, A-Z, 0-9, underscore (_), hyphen (-), dot (.)
     * - Must not start with a digit
     * - Must not start with "__" (double underscore)
     *
     * @return string A valid, unique cookie name.
     */
    private function generateUniqueCookieName(): string
    {
        $allowedChars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-.';
        $maxLength = 24;
        $minRandom = 5; // ensure at least 5 random chars to keep uniqueness

        // Calculate remaining length after prefix
        $prefix = 'ps_';
        $prefixLength = strlen($prefix);
        $randomLength = $maxLength - $prefixLength;

        if ($randomLength < $minRandom) {
            // Prefix too long; fallback to ignore prefix and generate full random
            $prefix = '';
            $prefixLength = 0;
            $randomLength = $maxLength;
        }

        // Generate random part
        $randomPart = '';
        $charsCount = strlen($allowedChars) - 1;
        for ($i = 0; $i < $randomLength; $i++) {
            $randomPart .= $allowedChars[random_int(0, $charsCount)];
        }

        // Combine prefix + random part
        $cookieName = $prefix . $randomPart;

        // Ensure no double underscore at start (if prefix is empty or ends with underscore)
        if (strpos($cookieName, '__') === 0) {
            // Remove the second underscore or replace
            $cookieName = substr_replace($cookieName, '', 1, 1);
        }

        // Final length check - trim if too long (should not happen, but safe)
        if (strlen($cookieName) > $maxLength) {
            $cookieName = substr($cookieName, 0, $maxLength);
        }

        return $cookieName;
    }
}

<?php
namespace CLS;

if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;

class Lang_Selector_Widget extends Widget_Base {

    public function get_name() {
        return 'cls_lang_selector';
    }

    public function get_title() {
        return __('Language Selector (Polylang)', 'cls-polylang');
    }

    public function get_icon() {
        return 'eicon-globe';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    public function get_keywords() {
        return [ 'language', 'idioma', 'translate', 'polylang', 'selector' ];
    }

    protected function render() {
        echo do_shortcode('[custom_lang_selector]');
    }
}

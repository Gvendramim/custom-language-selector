<?php
/**
 * Plugin Name: Custom Language Selector for Polylang
 * Description: Seletor de idioma integrado ao Polylang.
 * Version: 1.0.0
 * Author: Gabriel Vendramim
 */

if ( ! defined( 'ABSPATH' ) ) exit;

final class CLS_Polylang_Plugin {

    const VERSION = '1.2.0';
    const SLUG = 'custom-language-selector';

    private $flag_map = [];

    public function __construct() {
        $this->flag_map = [
            'br' => 'br.svg',
            'pt' => 'pt.svg',
            'en' => 'us.svg',
            'gb' => 'gb.svg',
            'ie' => 'ie.svg',
        ];

        add_shortcode('custom_lang_selector', [ $this, 'render_selector' ]);
        add_action('wp_enqueue_scripts', [ $this, 'enqueue_assets' ]);
        add_action('elementor/widgets/register', [ $this, 'register_elementor_widget' ]);
        add_action('admin_notices', [ $this, 'admin_notices' ]);
    }

    public function admin_notices() {
        if ( ! function_exists('pll_the_languages') ) {
            echo '<div class="notice notice-warning"><p><strong>Custom Language Selector:</strong> Polylang não está ativo. Ative o Polylang para que o seletor funcione corretamente.</p></div>';
        }
    }

    public function enqueue_assets() {
        $ver = self::VERSION;
        wp_enqueue_style( 'cls-style', plugin_dir_url(__FILE__) . 'assets/style.css', [], $ver, 'all' );
        wp_enqueue_script( 'cls-script', plugin_dir_url(__FILE__) . 'assets/script.js', [], $ver, true );
    }

    private function get_flag_url( $code, $fallback = '' ) {
        $code = strtolower($code);
        if ( isset($this->flag_map[$code]) ) {
            return plugin_dir_url(__FILE__) . 'assets/flags/' . $this->flag_map[$code];
        }
        return $fallback;
    }

    public function render_selector( $atts = [] ) {
        if ( ! function_exists('pll_the_languages') ) {
            return '';
        }

        $langs = pll_the_languages([
            'dropdown'      => 0,
            'show_flags'    => 0,
            'show_names'    => 1,
            'hide_if_empty' => 0,
            'raw'           => 1
        ]);

        if ( empty($langs) || ! is_array($langs) ) {
            return '';
        }

        $current_code = function_exists('pll_current_language') ? pll_current_language() : '';
        $current_name = isset($langs[$current_code]['name']) ? $langs[$current_code]['name'] : __('Idioma', 'cls-polylang');
        $current_flag = $this->get_flag_url($current_code);

        ob_start();
        ?>
        <nav class="cls-lang-selector" aria-label="<?php esc_attr_e('Selecionar idioma', 'cls-polylang'); ?>">
            <button class="cls-toggle" type="button" aria-haspopup="true" aria-expanded="false" aria-controls="cls-lang-menu">
                <span class="cls-current">
                    <?php if ( $current_flag ) : ?>
                        <img src="<?php echo esc_url($current_flag); ?>" alt="" class="cls-flag" />
                    <?php endif; ?>
                    <?php echo esc_html( $current_name ); ?>
                </span>
            </button>
            <ul class="cls-menu" id="cls-lang-menu" role="menu" hidden>
                <?php foreach ( $langs as $code => $lang ) :
                    $is_current = !empty($lang['current_lang']);
                    $url = isset($lang['url']) ? $lang['url'] : '#';
                    $name = isset($lang['name']) ? $lang['name'] : strtoupper($code);
                    $flag_url = $this->get_flag_url($code);
                ?>
                <li role="none">
                    <a role="menuitem"
                       href="<?php echo esc_url($url); ?>"
                       hreflang="<?php echo esc_attr($code); ?>"
                       <?php if ( $is_current ) echo 'aria-current="true"'; ?>
                       class="cls-item<?php echo $is_current ? ' is-current' : ''; ?>">
                        <?php if ( $flag_url ) : ?>
                            <img src="<?php echo esc_url($flag_url); ?>" alt="" class="cls-flag" loading="lazy" decoding="async" />
                        <?php endif; ?>
                        <span class="cls-name"><?php echo esc_html($name); ?></span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <?php
        return ob_get_clean();
    }

    public function register_elementor_widget( $widgets_manager ) {
        if ( ! did_action('elementor/loaded') ) {
            return;
        }

        require_once __DIR__ . '/includes/class-lang-selector-widget.php';
        $widgets_manager->register( new \CLS\Lang_Selector_Widget() );
    }
}

new CLS_Polylang_Plugin();

<?php
if ( ! defined( 'ABSPATH' ) ) exit;


class ikoAccount extends ikoTravelElements {
    function __construct() {
        parent::__construct();
        $this->blockName = 'ikoaccount';
        add_action('init', array( $this,'gutenbergBlockRegistration' ) ); // Adding Gutenberg Block
        add_shortcode( $this->blockName, array( $this,'blockHandler') );
        add_filter('ikoShortcodes',array( $this, 'shortcodeOutput') );
    }
    function shortcodeOutput($shortcodes) {
        $shortcodes[] = '['.$this->blockName.']';
        return $shortcodes;
    }
    function blockHandler($atts) {
        $this->coreFunction();
        return $this->ikoTravelElement();
    }
    function ikoTravelElement() {
        ob_start();
        ?><iko-account-button></iko-account-button><?php
        $content = ob_get_contents();
        ob_end_clean();
        $isAdmin = false;
        if (!empty($_REQUEST['context']) && $_REQUEST['context'] == 'edit') {
            $isAdmin = true;
        }
        if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'edit') {
            $isAdmin = true;
        }
        if (is_admin( ) || $isAdmin) {
            return htmlspecialchars($content);
        }
        return $content;
    }

    function gutenbergBlockRegistration() {
        // Skip block registration if Gutenberg is not enabled/merged.
        if (!function_exists('register_block_type')) {
            return;
        }
        
        $dir = dirname(__FILE__);

        $gutenbergJS = $this->blockName.'.js';
        wp_register_script('ikoTravelBlockRenderer_'.$this->blockName, $this->pluginURL . 'elements/js/'.$gutenbergJS,
            array(
                'wp-blocks',
                'wp-i18n',
                'wp-element',
                'wp-components',
                'wp-editor'
            ),
            false
        );

        $jsData = array(
            'blockCat'  => $this->namespace.'-blocks',
            'imgURL'    => $this->imgURL,
            'mode'      => $this->environmentVal
        );

        wp_localize_script( 'ikoTravelBlockRenderer_'.$this->blockName, 'ikoTravelData', $jsData );
        
        register_block_type('ikotravel-blocks/'.$this->blockName, array(
            'editor_script' => 'ikoTravelBlockRenderer_'.$this->blockName,
            'render_callback' => array($this,'blockHandler'),
            'attributes' => [],
            'category' => $this->namespace.'-blocks'
        ));
    }
}

$ikoAccount = new ikoAccount();

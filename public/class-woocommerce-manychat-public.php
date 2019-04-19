<?php

/**
* The public-facing functionality of the plugin.
*
* @link       http://fsylum.net
* @since      1.0.0
*
* @package    woocommerce_manychat
* @subpackage woocommerce_manychat/public
*/

/**
* The public-facing functionality of the plugin.
*
* Defines the plugin name, version, and two examples hooks for how to
* enqueue the admin-specific stylesheet and JavaScript.
*
* @package    woocommerce_manychat
* @subpackage woocommerce_manychat/public
* @author     Firdaus Zahari <firdaus@fsylum.net>
*/
class woocommerce_manychat_Public {

    /**
    * The ID of this plugin.
    *
    * @since    1.0.0
    * @access   private
    * @var      string    $plugin_name    The ID of this plugin.
    */
    private $plugin_name;

    /**
    * The version of this plugin.
    *
    * @since    1.0.0
    * @access   private
    * @var      string    $version    The current version of this plugin.
    */
    private $version;

    /**
    * The options name to be used in this plugin
    *
    * @since  	1.0.0
    * @access 	private
    * @var  	string 		$option_name 	Option name of this plugin
    */
    private $option_name = 'woocommerce_manychat';

    /**
    * Initialize the class and set its properties.
    *
    * @since    1.0.0
    * @param      string    $plugin_name       The name of the plugin.
    * @param      string    $version    The version of this plugin.
    */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
    * Register the stylesheets for the public-facing side of the site.
    *
    * @since    1.0.0
    */
    public function enqueue_styles() {

        /**
        * This function is provided for demonstration purposes only.
        *
        * An instance of this class should be passed to the run() function
        * defined in woocommerce_manychat_Loader as all of the hooks are defined
        * in that particular class.
        *
        * The woocommerce_manychat_Loader will then create the relationship
        * between the defined hooks and the functions defined in this
        * class.
        */

        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-manychat-public.css', array(), $this->version, 'all' );

    }

    /**
    * Register the stylesheets for the public-facing side of the site.
    *
    * @since    1.0.0
    */
    public function enqueue_scripts() {

        /**
        * This function is provided for demonstration purposes only.
        *
        * An instance of this class should be passed to the run() function
        * defined in woocommerce_manychat_Loader as all of the hooks are defined
        * in that particular class.
        *
        * The woocommerce_manychat_Loader will then create the relationship
        * between the defined hooks and the functions defined in this
        * class.
        */

        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-manychat-public.js', array( 'jquery' ), $this->version, false );

    }

    /**
    * Just embeds the header code in the header
    *
    * @since    1.0.0
    */
    public function the_embedder( $record ) {
        $script = get_option($this->option_name . '_integration');
        echo($script);
    }

    /**
    * Tries to get a Manychat ID. It tries hard.
    *
    * @since    1.0.0
    */
    public function the_id_getter( $record ) {
        $the_var = get_option($this->option_name . '_mc_id_variable');
        $url_var = (isset($_GET[$the_var]) && $_GET[$the_var] != "") ? htmlspecialchars($_GET[$the_var]) : NULL;
        if($url_var){
            ?>
            <script>
            var d = new Date();
            d.setTime(d.getTime() + (365*2*24*60*60*1000));
            var expires = "expires="+ d.toUTCString();
            document.cookie = "mc_id=<?php echo($url_var); ?>;" + expires + ";path=/";
            </script>
            <?php
        }else{
            if(isset($_COOKIE["mc_ref"] && $_COOKIE["mc_ref"] != ""){
                $response = wp_remote_post( $url, array(
                    'body'    => array(
                        "user_ref" => $_COOKIE["mc_ref"]
                    ),
                    'headers' => array(
                        'Authorization' => 'Bearer ' . get_option($this->option_name . '_api_key')
                    )
                ));
                echo($response);
            }
        }
    }

}

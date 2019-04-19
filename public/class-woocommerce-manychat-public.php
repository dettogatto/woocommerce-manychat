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
    * @access   private
    * @var      string    $plugin_name    The ID of this plugin.
    */
    private $plugin_name;

    /**
    * The version of this plugin.
    *
    * @access   private
    * @var      string    $version    The current version of this plugin.
    */
    private $version;

    /**
    * The options name to be used in this plugin
    *
    * @access 	private
    * @var  	string 		$option_name 	Option name of this plugin
    */
    private $option_name = 'woocommerce_manychat';

    /**
    * used for memoization
    *
    * @access 	private
    * @var  	array
    */
    private $usrinfo = NULL;

    /**
    * Initialize the class and set its properties.
    *
    * @param      string    $plugin_name       The name of the plugin.
    * @param      string    $version    The version of this plugin.
    */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
    * Register the stylesheets for the public-facing side of the site.
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
    * Gives a tag to the current User on Manychat
    */
    private function set_tag( $name ) {
        $response = wp_remote_post( 'https://api.manychat.com/fb/subscriber/addTagByName', array(
            'body' => array(
                "subscriber_id" => $_COOKIE["mc_id"],
                "tag_name" => $name,
            ),
            'headers' => array(
                'accept' => 'application/json',
                'Authorization' => 'Bearer ' . get_option($this->option_name . '_api_key')
            )
        ));
        if($response && $response["body"]){
            $res_body = json_decode($response["body"]);
            var_dump($res_body);
            return ($res_body->status == "success");
        }
        return false;
    }

    /**
    * Sets custom field values on Manychat
    */
    private function set_customfield( $field, $value ) {
        $response = wp_remote_post( 'https://api.manychat.com/fb/subscriber/setCustomFieldByName', array(
            'body' => array(
                "subscriber_id" => $_COOKIE["mc_id"],
                "field_name" => $field,
                "field_value" => $value
            ),
            'headers' => array(
                'accept' => 'application/json',
                'Authorization' => 'Bearer ' . get_option($this->option_name . '_api_key')
            )
        ));
        if($response && $response["body"]){
            $res_body = json_decode($response["body"]);
            return ($res_body->status == "success");
        }
        return false;
    }

    /**
    * Gets user info from Manychat
    */
    private function get_userinfo() {
        if($this->usrinfo){
            return $this->usrinfo;
        }
        $response = wp_remote_get( 'https://api.manychat.com/fb/subscriber/getInfo?subscriber_id='.$_COOKIE["mc_id"], array(
            'headers' => array(
                'accept' => 'application/json',
                'Authorization' => 'Bearer ' . get_option($this->option_name . '_api_key')
            )
        ));
        if($response && $response["body"]){
            $res_body = json_decode($response["body"]);
            if($res_body->status == "success"){
                $this->usrinfo = $res_body;
                return $res_body;
            }
        }
        return false;
    }

    /**
    * Gets user custom field value from Manychat
    */
    private function get_customfield( $name ) {
        $info = $this->get_userinfo();
        if(!$info){return false;}
        $fields = $info->data->custom_fields;
        foreach($fields as $k => $v){
            if($v->name == $name){
                return $v->value;
            }
        }
    }

    /**
    * Gets user tags from Manychat as an array
    */
    private function get_tags() {
        $info = $this->get_userinfo();
        if(!$info){return false;}
        $tags = $info->data->tags;
        $res = array();
        foreach($tags as $k => $v){
            $res[] = $v->name;
        }
        return $res;
    }

    /**
    * Just embeds the header code in the header
    */
    public function the_embedder( $record ) {
        $script = get_option($this->option_name . '_integration');
        echo($script);
    }

    /**
    * Tries to get a Manychat ID. It tries hard.
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
            if(isset($_COOKIE["mc_ref"]) && $_COOKIE["mc_ref"] != ""){
                $response = wp_remote_get( 'https://api.manychat.com/fb/subscriber/getInfoByUserRef?user_ref='.$_COOKIE["mc_ref"], array(
                    'headers' => array(
                        'accept' => 'application/json',
                        'Authorization' => 'Bearer ' . get_option($this->option_name . '_api_key')
                    )
                ));
                if($response && $response["body"]){
                    $res_body = json_decode($response["body"]);
                    if($res_body->status == "success"){
                        ?>
                        <script>
                        var d = new Date();
                        d.setTime(d.getTime() + (365*2*24*60*60*1000));
                        var expires = "expires="+ d.toUTCString();
                        document.cookie = "mc_id=<?php echo($res_body->data->id); ?>;" + expires + ";path=/";
                        </script>
                        <?php
                    }
                }
            }
        }
    }

    /**
    * Updates the cart list on Manychat
    */
    public function on_add_to_cart( $record ) {
        $this->set_tag("AZIONE: Aggiunta al carrello");
    }






}

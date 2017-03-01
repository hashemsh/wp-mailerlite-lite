<?php
class WPMI_Public {

    public function __construct() {

	}


	public function enqueue_scripts() {

        wp_enqueue_style( 'wpmi-public-style',WPMI_URL . 'assets/public/css/wpmi.css', array(), WP_Mailerlite::get_version(), $media = 'all' );

        wp_enqueue_script( 'wpmi-public-js', WPMI_URL.'assets/public/js/wpmi.js', array('jquery'), WP_Mailerlite::get_version(), true );
    	wp_localize_script( 'wpmi-public-js', 'wpmi_ajax', array(
    		'url' => admin_url( 'admin-ajax.php' ),
    		'nonce' => wp_create_nonce( 'ajax-nonce' ),
    		)
    	);

	}

    public function ajax_add_subscriber() {
        $nonce = ( isset($_POST['nonce']) && !empty($_POST['nonce']) ) ? $_POST['nonce'] : false ;
        $email = ( isset($_POST['email']) && !empty($_POST['email']) ) ? strip_tags($_POST['email']) : false ;
        $group = ( isset($_POST['group']) && !empty($_POST['group']) ) ? strip_tags($_POST['group']) : false ;
        $name = ( isset($_POST['name']) && !empty($_POST['name']) ) ? strip_tags($_POST['name']) : false ;
        $mobile = ( isset($_POST['mobile']) && !empty($_POST['mobile']) ) ? strip_tags($_POST['mobile']) : false ;
        if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) )
            die ( 'Nope!' );

        if ($email == false || $group == false) {
            wp_send_json_error( __('Please insert your email address', 'wpmi') );
        }

        $api_key = WPMI_Admin::get_option('api_key');
        $register_group = $group;
        $groups_api = (new \MailerLiteApi\MailerLite($api_key))->groups();
        if (isset($api_key) && !empty($api_key) && isset($register_group) && !empty($register_group)) {
            $subscriber = [
              'email' => $email,
              'fields' => [
                  'name' => $name,
                  'phone' => $mobile,
              ],
            ];
            $added_subscriber = $groups_api->addSubscriber($register_group, $subscriber);
            wp_send_json_success($added_subscriber);
        }
        wp_send_json_error( __('Something goes wrong', 'wpmi') );

    }


}

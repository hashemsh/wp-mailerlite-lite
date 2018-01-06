<?php
class WPMI_Admin
{
    public $group_api;

    public function __construct()
    {
        $this->group_api = (new \MailerLiteApi\MailerLite(self::get_option('api_key')))->groups();
    }
    
    public function enqueue_scripts()
    {
        wp_enqueue_style('wpmi-select2', WPMI_URL.'assets/admin/css/select2.min.css', array(), '4.0.3', $media = 'all');
        wp_enqueue_style('wpmi-admin-style', WPMI_URL.'assets/admin/css/wpmi_admin.css', array(), WP_Mailerlite::get_version(), $media = 'all');

        wp_enqueue_script('wpmi-select2', WPMI_URL.'assets/admin/js/select2.min.js', array('jquery'), '4.0.3', true);
        wp_enqueue_script('wpmi-admin-js', WPMI_URL.'assets/admin/js/wpmi_admin.js', array('jquery'), WP_Mailerlite::get_version(), true);
        wp_localize_script('wpmi-admin-js', 'wpmi_ajax', array(
            'url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ajax-nonce'),
            'select_groups' => __('Select your groups', 'wpmi'),
            'select_user' => __('Enter user email or id', 'wpmi'),
            'error' => __('Something goes wrong. please try again', 'wpmi'),
            )
        );
    }

    public function admin_menu()
    {
        add_menu_page(
            __('WP Mailerlite', 'wpmi'),
            __('WP Mailerlite', 'wpmi'),
            'manage_options',
            'wpmi',
            array($this, 'main_menu'),
            'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAPCAMAAADTRh9nAAABEVBMVEUAAAAAn1AApUsAnFIAm00AoFAAoVQAoFQAoFMAo1MAoVQAoVMAoFQAoFQAoVQAoFQAoFMAoVQAoVMAoVQAoFIAoFMAoVMAoVQBoFICoFMDoVQEoVQIoVUIolcIo1gKo1gMo1gQpFoSpFkUpl4Wpl8XqGEZpl8Zp18Zp2AaqGIbqGIcqGIdqWMfqWQfqmUkrGgorWssrm0ysXI2snM4s3VIuYBJuYFKuYFPu4RVu4Vdv4xfwI5fwY9hwI5hwZBlxJRpxZZqxZdtxZVvxpiEzaaIzqiL0auQ0q6R07CY1raq3cGs3sSv3sSz4Mi44szI6NbL6tnN6trb8OTe8ebg8unh8unn9e3r9vD3/Pn6/fv7/fzb8Z5rAAAAFHRSTlMAEBEfISNPYWZmanLl6Ozw+Pn7/Kl/NtMAAACXSURBVBjTY2Dg5BNGBTxsDEwC4uiAl4EFSBoqi+qLIgSFQYKi3vYaIQqieqJoglKWEs5BVrLy1o5aCEG1CCWvMDdFPx/3UBUkQRmzABGdSFeXYBs0Qe0oJztbTbigeoSMSbiHrm+gp78BRFDMSFXSVFTU3EZO2sLBWBQiiA74sQgKcgEFhblZUQAjAwOLEAcDBmBmxxQDAOB8II05mE7DAAAAAElFTkSuQmCC',
            76
        );

    }

    public function widgets_init()
    {
        register_widget('WPMI_Widget');
    }

    public function main_menu()
    {
        ?>
        <div class="wrap wpmi_wrap">
            <h1><?php _e('WP Mailerlite Integration', 'wpmi'); ?></h1>
            <?php
                settings_errors();
                $active_tab = isset($_GET[ 'tab' ]) ? $_GET[ 'tab' ] : 'general';
            ?>
            <h2 class="nav-tab-wrapper" style="margin-bottom: 10px">
                <a href="?page=wpmi&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>"><?php _e('General Settings', 'wpmi') ?></a>
            </h2>
            <form method="post" action="options.php">
                <?php
                    settings_fields('wpmi_group');
                    do_settings_sections('wpmi_general_page');
                    submit_button();
                ?>
            </form>
        <?php

    }

    public function admin_init()
    {
        register_setting(
            'wpmi_group', // option group
            'wpmi_options', // option name
            array($this, 'sanitize')  // sanitize callback
        );

        // Settings Sections
        add_settings_section(
            'wpmi_general',
            __('General Settings', 'wpmi'),
            null,
            'wpmi_general_page'
        );

        // Settings fields
        add_settings_field(
            'wpmi_api_key',
            __('Mailelite API Key', 'wpmi'),
            array($this, 'api_key_callback'),
            'wpmi_general_page',
            'wpmi_general' 
        );
        add_settings_field(
            'wpmi_reg_group',
            __('Register Users Groups', 'wpmi'),
            array($this, 'register_group_callback'),
            'wpmi_general_page',
            'wpmi_general' 
        );
        if( class_exists('GFForms') ) {
	        add_settings_field(
		        'wpmi_gravity_forms',
		        __('Gravityforms', 'wpmi'),
		        array($this, 'gravity_forms_callback'),
		        'wpmi_general_page',
		        'wpmi_general' 
	        );
        }

    }

    public function api_key_callback()
    {
        printf(
            '<input class="regular-text" type="text" name="wpmi_options[api_key]" id="wpmi_api_key" value="%s"> <p class="description">'.__('You can get it from <a href="%s" target="_blank">Here</a>', 'wpmi').'</p>',
            self::get_option('api_key'),
            'https://app.mailerlite.com/integrations/api/'
        );
    }

    public function register_group_callback()
    {
        $api_key = self::get_option('api_key');
        if( isset($api_key) && !empty($api_key) ) {
	        $all_groups = $this->group_api->get();
	        if ($all_groups) {
		        echo '<select name="wpmi_options[register_groups][]" id="register_groups" multiple="multiple" class="wpmi_select2">';
		        echo '<option value="" >'.__('Select groups', 'wpmi').'</option>';
		        foreach ($all_groups as $group) {
			        $selected = (in_array($group->id, self::get_option('register_groups'))) ? ' selected="selected"' : '';
			        echo '<option value="'.$group->id.'" '.$selected.'>'.$group->name.' ( '.$group->total.' '.__('User', 'wpmi').')</option>';
		        }
		        echo '</select>';
	        }
        } else {
            _e('Please insert your api key first.', 'wpmi');
        }

    }

    public function gravity_forms_callback()
    {
        $api_key = self::get_option('api_key');
        $options = self::get_option('all');
        if( isset($api_key) && !empty($api_key) ) {
	        $forms = GFAPI::get_forms();
	        if ($forms) {
		        echo '<select name="wpmi_options[gravity][form_id]" id="register_groups" class="wpmi_select2" data-placeholder="'.__('Select gravity form', 'wpmi').'">';
		        echo '<option value="" >'.__('Select gravity form', 'wpmi').'</option>';
			        foreach ($forms as $form) {
				        $form_id = $form['id'];
				        $form_title = $form['title'];
				        $selected = ($form_id == self::get_option('gravity_form_id')) ? ' selected="selected"' : '';
				        echo '<option value="'.$form_id.'" '. $selected.'>'.$form_title.'</option>';
			        }
		        echo '</select>';
	        }

	        $all_groups = $this->group_api->get();
	        if ($all_groups) {
		        echo '<select name="wpmi_options[gravity][group][]" id="register_groups" multiple="multiple" class="wpmi_select2" data-placeholder="'.__('Select groups', 'wpmi').'">';
		        echo '<option value="" >'.__('Select groups', 'wpmi').'</option>';
		        foreach ($all_groups as $group) {
			        $selected = (in_array($group->id, self::get_option('gravity_form_group'))) ? ' selected="selected"' : '';
			        echo '<option value="'.$group->id.'" '.$selected.'>'.$group->name.' ( '.$group->total.' '.__('User', 'wpmi').')</option>';
		        }
		        echo '</select>';
	        }

        } else {
            _e('Please insert your api key first.', 'wpmi');
        }

    }

	public function add_setting_link_meta( $links, $file ) {
		if ( strpos( $file, 'wp-mailerlite-lite.php' ) !== false ) {
			$new_links = array(
				'donate' => '<a href="'.admin_url( 'admin.php?page=wpmi').'" >'.__('Settings', 'wpmi').'</a>',
			);

			$links = array_merge( $links, $new_links );
		}

		return $links;
    }

    public static function get_option($option_name = 'all')
    {
        $options = get_option('wpmi_options');
        $api_key = (isset($options['api_key']) && !empty($options['api_key'])) ? $options['api_key'] : '';
        $register_groups = (isset($options['register_groups']) && !empty($options['register_groups'])) ? $options['register_groups'] : array();
        $gravity_form_id = (isset($options['gravity']['form_id']) && !empty($options['gravity']['form_id'])) ? $options['gravity']['form_id'] : false;
        $gravity_form_group = (isset($options['gravity']['group']) && !empty($options['gravity']['group'])) ? $options['gravity']['group'] : array();

        switch ($option_name) {
            case 'api_key':
                return $api_key;
                break;
            case 'register_groups':
                return $register_groups;
                break;
            case 'gravity_form_id':
                return $gravity_form_id;
                break;
            case 'gravity_form_group':
                return $gravity_form_group;
                break;
            default:
                return $options;
                break;
        }
    }
}

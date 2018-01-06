<?php

class WPMI_Actions
{
    private $api_key;
    private $register_groups;
    private $groups_api;
    private $subscribers_api;

    public function __construct()
    {
        $this->api_key = WPMI_Admin::get_option('api_key');
        $this->register_groups = WPMI_Admin::get_option('register_groups');
        $this->groups_api = (new \MailerLiteApi\MailerLite($this->api_key))->groups();
        $this->subscribers_api = (new \MailerLiteApi\MailerLite($this->api_key))->subscribers();

        $this->define_hooks();
    }


    private function define_hooks()
    {
        add_action('user_register', array($this, 'user_register'));
	    add_action('gform_after_submission', array($this, 'gravity_submission'), 10, 2);

    }

    /*
     * Add registred users to lists
    */
    public function user_register($user_id)
    {
        // Add user after register
        if (isset($this->register_groups) && !empty($this->register_groups) && isset($this->api_key) && !empty($this->api_key)) {
            $user_info = get_userdata($user_id);
            $subscriber = [
              'email' => $user_info->user_email,
              'fields' => [
                  'name' => ($user_info->first_name) ? $user_info->first_name : $user_info->display_name,
                  'last_name' => ($user_info->last_name) ? $user_info->last_name : '',
              ],
            ];
            foreach ($this->register_groups as $key => $group) {
                $this->groups_api->addSubscriber($group, $subscriber);
            }
        }

    }

	/*
	 * Add users to list after submission a form
	*/
	public function gravity_submission( $entry, $form ) {
		$fields = $form['fields'];
		$form_title = $form['title'];
		$form_id = $form['id'];
		$entry_id = $entry['id'];
		$gravityforms_settings = $this->gravityforms;
		if (array_key_exists($form_id, $gravityforms_settings)) {
			$this_form_option = $gravityforms_settings[$form_id];
			$email = false;
			$name = false;
			$last_name = false;
			$company = false;
			$country = false;
			$city = false;
			$phone = false;
			$state = false;
			$zip = false;
			foreach ($this_form_option as $mailerlite_id => $field_id) {
				if ($field_id!='none') {
					switch ($mailerlite_id) {
						case 'email':
							$email = $entry[$field_id];
							break;
						case 'name':
							$name = $entry[$field_id];
							break;
						case 'last_name':
							$last_name = $entry[$field_id];
							break;
						case 'company':
							$company = $entry[$field_id];
							break;
						case 'country':
							$country = $entry[$field_id];
							break;
						case 'city':
							$city = $entry[$field_id];
							break;
						case 'phone':
							$phone = $entry[$field_id];
							break;
						case 'state':
							$state = $entry[$field_id];
							break;
						case 'zip':
							$zip = $entry[$field_id];
							break;
					}
				}
			}
			if ( isset($this_form_option['groups']) && !empty($this_form_option['groups']) ) {
				$groups = $this_form_option['groups'];
				$subscriber = array(
					'email' => $email,
					'fields' => array()
				);
				if (isset($name) && !empty($name)) {
					$subscriber['fields']['name'] = $name;
				}
				if (isset($last_name) && !empty($last_name)) {
					$subscriber['fields']['last_name'] = $last_name;
				}
				if (isset($company) && !empty($company)) {
					$subscriber['fields']['company'] = $company;
				}
				if (isset($country) && !empty($country)) {
					$subscriber['fields']['country'] = $country;
				}
				if (isset($city) && !empty($city)) {
					$subscriber['fields']['city'] = $city;
				}
				if (isset($phone) && !empty($phone)) {
					$subscriber['fields']['phone'] = $phone;
				}
				if (isset($state) && !empty($state)) {
					$subscriber['fields']['state'] = $state;
				}
				if (isset($zip) && !empty($zip)) {
					$subscriber['fields']['zip'] = $zip;
				}
				if ( isset($email) && !empty($email) ) {
					foreach ($groups as $key => $group) {
						$added_subscriber = $this->groups_api->addSubscriber($group, $subscriber);
					}
				}
			}
		}
	}

}




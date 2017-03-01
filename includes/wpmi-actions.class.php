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


}




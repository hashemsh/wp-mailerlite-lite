<?php
class WPMI_i18n {
	public function load_plugin_textdomain() {

        $plugin_rel_path = plugin_basename( WPMI_PATH ).'/languages';
        load_plugin_textdomain( 'wpmi', false, $plugin_rel_path );

	}

}

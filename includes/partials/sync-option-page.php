<p><?php _e('From this section you can sync old users to mailerlite. if you have a lot of users, it may be too long.', 'wpmi') ?></p>
<form class="wpmi_sync_users" action="" method="post">
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row"><?php _e('Please select your action:', 'wpmi') ?></th>
                <td>
                    <input type="radio" id="wpmi_select_user" value="wpmi_select_user" name="wpmi_user_sync_action">
                    <label for="wpmi_select_user"><?php _e('Select Users', 'wpmi'); ?></label>
                    <input type="radio" id="wpmi_selected_role" value="wpmi_selected_role" name="wpmi_user_sync_action">
                    <label for="wpmi_selected_role"><?php _e('Select Role', 'wpmi'); ?></label>
                </td>
            </tr>
            <tr class="wpmi_select_user wpmi_conditional_view">
                <th scope="row"><?php _e('User ID', 'wpmi') ?></th>
                <td>
                    <select name="" id="wpmi_sync_user_id" class="wpmi_search_users" multiple></select>
                </td>
            </tr>
            <tr class="wpmi_selected_role wpmi_conditional_view">
                <th scope="row"><?php _e('User Role', 'wpmi') ?></th>
                <td>
                    <select name="" id="wpmi_sync_role" class="wpmi_select2">
                        <?php wp_dropdown_roles( 'subscriber' ); ?>
                    </select>
                </td>
            </tr>
            <tr class="wpmi_conditional_view wpmi_group">
                <th scope="row"><?php _e('Group', 'wpmi'); ?></th>
                <td>
                    <?php
                        $all_groups = $this->group_api->get();
                        if ($all_groups) {
                            echo '<select name="wpmi_sync_group" id="wpmi_sync_group" multiple="multiple" class="wpmi_select2">';
                            foreach ($all_groups as $group) {
                                $selected = (in_array($group->id, self::get_option('customers_groups'))) ? ' selected="selected"' : '';
                                echo '<option value="'.$group->id.'" '.$selected.'>'.$group->name.' ( '.$group->total.' '.__('User', 'wpmi').')</option>';
                            }
                            echo '</select>';
                        }
                    ?>
                </td>
            </tr>
        </tbody>
    </table>
    <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Synchronize', 'wpmi') ?>"></p>
    <div class="wpmi_sync_user_result"></div>
</form>
<script type="text/javascript">
	jQuery(document).ready(function($) {

		jQuery(document).on('submit', 'form.wpmi_sync_users', function(event) {
			event.preventDefault();
			var user_id = jQuery('#wpmi_sync_user_id').val();
			var role = jQuery('#wpmi_sync_role').val();
			var groups = jQuery('#wpmi_sync_group').val();
			var actionType = jQuery('input[name="wpmi_user_sync_action"]:checked').val();
			var thisForm = jQuery(this);
			thisForm.find('#submit').addClass("disabled");
			$('.wpmi_sync_user_result').html('<div class="wpmiload-speeding-wheel"></div>');
            jQuery.ajax({
                url: wpmi_ajax.url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'wpmi_sync_user',
                    nonce: wpmi_ajax.nonce,
                    user_id: user_id,
                    role: role,
                    groups: groups,
                    action_type: actionType,
                }
            })
            .done(function (result) {
                if( result.success == true ) {
                    $('.wpmi_sync_user_result').html(result.data);
                } else {
                    $('.wpmi_sync_user_result').html('<div class="wpmi_danger">'+result.data+'</div>');
                }
            })
            .fail(function (result) {
                console.log('fail');
            })
            .always(function (result) {
                thisForm.find('#submit').removeClass("disabled");
            })


		});
	});
</script>

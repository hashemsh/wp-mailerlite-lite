<?php
$gravityforms = self::get_option('gravityforms');
$forms = GFAPI::get_forms();
$mailerlite_fields = array(
    'email' => __('E-mail','wpmi'),
    'name' => __('Name','wpmi'),
    'last_name' => __('Last Name','wpmi'),
    'company' => __('Company','wpmi'),
    'country' => __('Country','wpmi'),
    'city' => __('City','wpmi'),
    'phone' => __('Phone','wpmi'),
    'state' => __('State','wpmi'),
    'zip' => __('ZIP','wpmi'),
);
?>
<?php if (empty($forms)): ?>
    <p><?php _e('You have not any form yet! Pleade creat it first.', 'wpmi') ?></p>
<?php else: ?>
    <?php
        foreach ($forms as $key => $form):
            $form_id = $form['id'];
            $form_title = $form['title'];
            $form_fields = $form['fields'];
    ?>
    <div class="wpmi_gravity_form_container">

        <h3>
            <?php echo $form_title; ?>
            <?php
                $all_groups = $this->group_api->get();
                if (isset($all_groups)) {
                    echo '<select name="wpmi_options[gravityforms]['.$form_id.'][groups][]" id="mailelite_group" multiple="multiple" class="wpmi_select2">';
                    foreach ($all_groups as $key => $group) {
                        $selected = '';
                        if( !empty($gravityforms) ) {
                            $groups = ( array_key_exists('groups', $gravityforms[$form_id]) ) ? $gravityforms[$form_id]['groups'] : 0 ;
                            $selected = (in_array($group->id, $groups)) ? ' selected="selected"' : '';
                        }
                        echo '<option value="'.$group->id.'" '.$selected.'>'.$group->name.' ( '.$group->total.' '.__('User', 'wpmi').')</option>';
                    }
                    echo '</select>';
                }
            ?>
            <span class="dashicons dashicons-arrow-down-alt2"></span>
        </h3>
        <div class="wpmi_gravity_fields_container">
            <table>
                <thead>
                    <tr>
                        <th><?php _e('Mailerlite Field', 'wpmi') ?></th>
                        <th><?php _e('Form Field', 'wpmi') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mailerlite_fields as $mailerlite_key => $mailerlite_field): ?>
                        <tr>
                            <td><?php echo $mailerlite_field; ?>:</td>
                            <td>
                                <?php
                                    $options = '<option value="none">'.__('Select a field', 'wpmi').'</option>';
                                    $selected = ( array_key_exists($form_id, $gravityforms) ) ? $gravityforms[$form_id][$mailerlite_key] : 'none' ;
                                    foreach ($form_fields as $key => $field) {
                                        $options .= '<option value="'.$field->id.'" '.selected( $selected, $field->id, false ).'>'.$field->label.'</option>';
                                    }
                                ?>
                                <select class="" name="wpmi_options[gravityforms][<?php echo $form_id ?>][<?php echo $mailerlite_key ?>]">
                                    <?php echo $options ?>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div><!-- wpmi_gravity_fields_container -->
    </div><!-- wpmi_gravity_form_container -->
    <?php endforeach; ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            jQuery(document).on('click', '.wpmi_gravity_form_container h3 span.dashicons', function(event) {
                event.preventDefault();
                jQuery(this).toggleClass('active');
                jQuery(this).parent().parent().find('.wpmi_gravity_fields_container').slideToggle(300);
            });
        });
    </script>
<?php endif; ?>

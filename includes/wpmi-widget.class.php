<?php

class WPMI_Widget extends WP_Widget {
	function __construct() {
		parent::__construct(
			'wpmi-widget',
			__('Mailerlite Widget', 'wpmi'),
			array( 'description' => __( 'Displays mailerlite subscription form', 'wpmi' ), )
		);
	}
	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

     	echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		?>
        <div class="wpmi_subscription">
            <?php echo apply_filters( 'the_content', $instance['before_text'] ) ?>
            <form action="#" class="wpmi_form" data-invalid="<?php _e('Your email is invalid', 'wpmi') ?>" data-failed="<?php _e('Oops! Something went wrong. Please try again later.', 'wpmi') ?>" data-group="<?php echo $instance['group_id']; ?>">
                <p class="wpmi_input_container">
                    <label for="wpmi_email_field"><?php _e('Your Email Address:', 'wpmi'); ?></label>
                    <input id="wpmi_email_field" type="email" class="wpmi_input_email form-control" placeholder="<?php _e('email@email.com', 'wpmi'); ?>">
                </p>
                <?php if ($instance['show_name'] == 'yes'): ?>
                    <p class="wpmi_input_container">
                        <label for="wpmi_email_field"><?php echo $instance['name_label']; ?></label>
                        <input id="wpmi_email_field" type="text" class="wpmi_input_name form-control">
                    </p>
                <?php endif; ?>
                <?php if ($instance['show_mobile'] == 'yes'): ?>
                    <p class="wpmi_input_container">
                        <label for="wpmi_email_field"><?php echo $instance['mobile_label']; ?></label>
                        <input id="wpmi_email_field" type="text" class="wpmi_input_mobile form-control">
                    </p>
                <?php endif; ?>
                <p class="wpmi_submit_container">
                    <button class="button btn wpmi_btn" data-success="<?php _e('Thank You! Your Email successfully added to our newletter', 'wpmi') ?>"><?php echo $instance['btn_text'] ?></button>
                </p>
                <div class="wpmiload-container"><div class="wpmiload-speeding-wheel"></div></div>
            </form>
        </div>
		<?php

		echo $args['after_widget'];
	}
	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'Subscription Form', 'wpmi' );
		}
		if ( isset( $instance[ 'group_id' ] ) ) {
			$group_id = $instance[ 'group_id' ];
		} else {
			$group_id = 0;
		}
		if ( isset( $instance[ 'before_text' ] ) ) {
			$before_text = $instance[ 'before_text' ];
		} else {
			$before_text = '';
		}
		if ( isset( $instance[ 'btn_text' ] ) ) {
			$btn_text = $instance[ 'btn_text' ];
		} else {
			$btn_text = __('Subscribe', 'wpmi');
		}
		if ( isset( $instance[ 'btn_class' ] ) ) {
			$btn_class = $instance[ 'btn_class' ];
		} else {
			$btn_class = '';
		}
		if ( isset( $instance[ 'email_label' ] ) ) {
			$email_label = $instance[ 'email_label' ];
		} else {
			$email_label = __('Your Email Address:', 'wpmi');
		}
		if ( isset( $instance[ 'show_name' ] ) ) {
			$show_name = $instance[ 'show_name' ];
		} else {
			$show_name = '';
		}
		if ( isset( $instance[ 'name_label' ] ) ) {
			$name_label = $instance[ 'name_label' ];
		} else {
			$name_label = __('Your Name:', 'wpmi');
		}
		if ( isset( $instance[ 'show_mobile' ] ) ) {
			$show_mobile = $instance[ 'show_mobile' ];
		} else {
			$show_mobile = '';
		}
        if ( isset( $instance[ 'mobile_label' ] ) ) {
			$mobile_label = $instance[ 'mobile_label' ];
		} else {
			$mobile_label = __('Your Mobile:', 'wpmi');
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'wpmi' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'before_text' ); ?>"><?php _e( 'Before form text:', 'wpmi' ); ?></label>
            <textarea rows="8" cols="40" id="<?php echo $this->get_field_id( 'before_text' ); ?>" name="<?php echo $this->get_field_name( 'before_text' ); ?>"><?php echo $before_text; ?></textarea>
        </p>
		<p>
			<label for="<?php echo $this->get_field_id( 'group_id' ); ?>"><?php _e( 'Subscribtion Group:', 'wpmi' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'group_id' ); ?>" name="<?php echo $this->get_field_name( 'group_id' ); ?>">
				<?php
                $group_api = (new \MailerLiteApi\MailerLite(WPMI_Admin::get_option('api_key')))->groups();
                $all_groups = $group_api->get();
                if ($all_groups) {
                    foreach ($all_groups as $group) {
                        echo '<option value="'.$group->id.'" '.selected( $group_id, $group->id, false ).'>'.$group->name.' ('.$group->total.__('User', 'wpmi').')</option>';
                    }
                }
                ?>
			</select>
		</p>
        <p>
			<label for="<?php echo $this->get_field_id( 'btn_text' ); ?>"><?php _e( 'Button Text:', 'wpmi' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'btn_text' ); ?>" name="<?php echo $this->get_field_name( 'btn_text' ); ?>" type="text" value="<?php echo esc_attr( $btn_text ); ?>">
		</p>
        <p>
            <label for="<?php echo $this->get_field_id( 'btn_class' ); ?>"><?php _e( 'Button class:', 'wpmi' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'btn_class' ); ?>" name="<?php echo $this->get_field_name( 'btn_class' ); ?>" type="text" value="<?php echo esc_attr( $btn_class ); ?>">
        </p>
        <p>
			<label for="<?php echo $this->get_field_id( 'email_label' ); ?>"><?php _e( 'Email field label:', 'wpmi' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'email_label' ); ?>" name="<?php echo $this->get_field_name( 'email_label' ); ?>" type="text" value="<?php echo esc_attr( $email_label ); ?>">
		</p>
        <p>
            <input class="widefat wpmi_widget_checkbox" id="<?php echo $this->get_field_id( 'show_name' ); ?>" name="<?php echo $this->get_field_name( 'show_name' ); ?>" type="checkbox" value="yes" <?php checked( $show_name, 'yes') ?>>
			<label for="<?php echo $this->get_field_id( 'show_name' ); ?>"><?php _e( 'Show name field', 'wpmi' ); ?></label>
		</p>
        <p style="display: <?php echo ($show_name == 'yes') ? 'block' : 'none' ; ?>">
			<label for="<?php echo $this->get_field_id( 'name_label' ); ?>"><?php _e( 'Name field label:', 'wpmi' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'name_label' ); ?>" name="<?php echo $this->get_field_name( 'name_label' ); ?>" type="text" value="<?php echo esc_attr( $name_label ); ?>">
        </p>
        <p>
            <input class="widefat wpmi_widget_checkbox" id="<?php echo $this->get_field_id( 'show_mobile' ); ?>" name="<?php echo $this->get_field_name( 'show_mobile' ); ?>" type="checkbox" value="yes" <?php checked( $show_mobile, 'yes') ?>>
			<label for="<?php echo $this->get_field_id( 'show_mobile' ); ?>"><?php _e( 'Show mobile field', 'wpmi' ); ?></label>
		</p>
        <p style="display: <?php echo ($show_mobile == 'yes') ? 'block' : 'none' ; ?>">
			<label for="<?php echo $this->get_field_id( 'mobile_label' ); ?>"><?php _e( 'Mobile field label:', 'wpmi' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'mobile_label' ); ?>" name="<?php echo $this->get_field_name( 'mobile_label' ); ?>" type="text" value="<?php echo esc_attr( $mobile_label ); ?>">
        </p>

		<?php
	}
	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['group_id'] = ( ! empty( $new_instance['group_id'] ) ) ? strip_tags( $new_instance['group_id'] ) : '';
		$instance['before_text'] = ( ! empty( $new_instance['before_text'] ) ) ?  stripslashes( $new_instance['before_text'] ) : '';
		$instance['btn_text'] = ( ! empty( $new_instance['btn_text'] ) ) ?  stripslashes( $new_instance['btn_text'] ) : '';
		$instance['btn_class'] = ( ! empty( $new_instance['btn_class'] ) ) ?  stripslashes( $new_instance['btn_class'] ) : '';
		$instance['email_label'] = ( ! empty( $new_instance['email_label'] ) ) ?  stripslashes( $new_instance['email_label'] ) : '';
		$instance['show_name'] = ( ! empty( $new_instance['show_name'] ) ) ?  stripslashes( $new_instance['show_name'] ) : '';
		$instance['name_label'] = ( ! empty( $new_instance['name_label'] ) ) ?  stripslashes( $new_instance['name_label'] ) : '';
		$instance['show_mobile'] = ( ! empty( $new_instance['show_mobile'] ) ) ?  stripslashes( $new_instance['show_mobile'] ) : '';
		$instance['mobile_label'] = ( ! empty( $new_instance['mobile_label'] ) ) ?  stripslashes( $new_instance['mobile_label'] ) : '';
		return $instance;
	}
} // class My_Widget

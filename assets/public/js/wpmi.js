jQuery(document).ready(function($) {
	jQuery(document).on('submit', '.wpmi_form', function(event) {
		event.preventDefault();
		var groupID = jQuery(this).data('group');
		var userEmail = jQuery(this).find('.wpmi_input_email').val();
		var userName = jQuery(this).find('.wpmi_input_name').val();
		var userMobile = jQuery(this).find('.wpmi_input_mobile').val();
		var form = jQuery(this);
		var formBtn = jQuery(this).find('.wpmi_btn');
		var btnText = formBtn.text();
		var successText = formBtn.data('success');
		var failedText = jQuery(this).data('failed');
		var invalidEmail = jQuery(this).data('invalid');
		var emailPattern = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
		jQuery('p.wpmi_result').remove();
		if (emailPattern.test(userEmail)) {
            form.addClass('wpmi_sending');
            form.find('button').attr('disabled', 'disabled');
            jQuery.ajax({
                url: wpmi_ajax.url,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'mailerlite_add_subscriber',
					nonce: wpmi_ajax.nonce,
					email: userEmail,
					name: userName,
					mobile: userMobile,
					group: groupID,
                }
            })
            .done(function(data) {
                form.removeClass('wpmi_sending');
                form.find('button').attr('disabled', false);
                console.log(data);
                if (data.success==true) {
                    form.after('<p class="wpmi_result success">'+successText+'</p>')
                } else {
                    formBtn.text(btnText);
    			   	form.after('<p class="wpmi_result failed">'+failedText+'</p>')
                }

            })
            .fail(function() {
                form.removeClass('wpmi_sending');
                form.find('button').attr('disabled', false);
                formBtn.text(btnText);
			   	form.after('<p class="wpmi_result failed">'+failedText+'</p>')
            })

		} else {
			jQuery(this).after('<p class="wpmi_result failed">'+invalidEmail+'</p>')
		}
	});
});

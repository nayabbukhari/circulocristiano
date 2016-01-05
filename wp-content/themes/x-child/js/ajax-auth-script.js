/**
 * Desc: Register pop up scripts
 * author: ajay3085006
 * dated: 23 Dec 2015
 * updated: 27 Dec 2015
 */
jQuery(document).ready(function ($) {
    // Display form from link inside a popup
	$('#pop_login, #pop_signup').live('click', function (e) {
        formToFadeOut = $('form#register');
        formtoFadeIn = $('form#login');
        if ($(this).attr('id') == 'pop_signup') {
            formToFadeOut = $('form#login');
            formtoFadeIn = $('form#register');
        }
        formToFadeOut.fadeOut(500, function () {
            formtoFadeIn.fadeIn();
        })
        return false;
    });
	// Close popup
    $(document).on('click', '.login_overlay, .close', function () {
		$('form#login, form#register , form#forgot_password_form').fadeOut(500, function () {
            $('.login_overlay').remove();
        });
        return false;
    });
   

    // Show the login/signup popup on click
    $('#show_login, #show_signup, .show-register').on('click', function (e) {
        $('body').prepend('<div class="login_overlay"></div>');
        if ($(this).attr('id') == 'show_login') 
			$('form#login').fadeIn(500);
        else 
			$('form#register').fadeIn(500);
        e.preventDefault();
    });

	// Perform AJAX login/register on form submit
	$('form#login, form#register').on('submit', function (e) {
	
        if (!$(this).valid()) return false;
        $('p.status', this).show().text(ajax_auth_object.loadingmessage);
		action = 'ajaxlogin';
		username = 	$('form#login #username').val();
		password = $('form#login #password').val();
		email = '';
		security = $('form#login #security').val();
		blog_id	=	0; 
		if ($(this).attr('id') == 'register') {
			action = 'ajaxregister';
			username = $('#signonname').val();
			password = $('#signonpassword').val();
        	email = $('#email').val();
        	security = $('#signonsecurity').val();	
        	blog_id = $('#x_church_name').val();	
        	password2 = $('#password2').val();	
			if(password!=password2){ $('p.status', this).show().text("Password should be same");return false;}
		}  
		ctrl = $(this);
		$.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajax_auth_object.ajaxurl,
            data: {
                'action': action,
                'username': username,
                'password': password,
				'email': email,
				'blog_id': blog_id,
                'security': security
            },
            success: function (data) {
				$('p.status', ctrl).text(data.message);
				if (data.loggedin == true) {
                    document.location.href = ajax_auth_object.redirecturl;
                }
            }
        });
        e.preventDefault();
    });
	
	// Client side form validation
    if (jQuery("#register").length) {
		//jQuery("#register").validate();
		
		$("#register").validate({
			rules: {
				signonpassword: {
					required: true,
					minlength: 5
				},
				password2: {
					required: true,
					minlength: 5,
					equalTo: "#signonpassword"
				},
			},
			messages: {
				signonpassword: {
					required: "Please provide a password",
					minlength: "Your password must be at least 5 characters long"
				},
				password2: {
					required: "Please provide a password",
					minlength: "Your password must be at least 5 characters long",
					equalTo: "Please enter same password as in password field"
				},
			}
		});
		}
    else if (jQuery("#login").length) {
		//jQuery("#login").validate();
		
		$("#login").validate({
			rules: {
				username: "required",
				password: "required",

			},
			messages: {
				username: "Please enter your username",
				password: "Please enter your password",
			}
		});
		}
		
});
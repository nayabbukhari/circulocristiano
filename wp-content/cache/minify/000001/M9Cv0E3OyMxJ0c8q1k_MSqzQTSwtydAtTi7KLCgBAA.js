
/* ajax-auth-script.js */

/* 1   */ /**
/* 2   *|  * Desc: Register pop up scripts
/* 3   *|  * author: ajay3085006
/* 4   *|  * dated: 23 Dec 2015
/* 5   *|  * updated: 27 Dec 2015
/* 6   *|  */
/* 7   */ jQuery(document).ready(function ($) {
/* 8   */     // Display form from link inside a popup
/* 9   */ 	$('#pop_login, #pop_signup').live('click', function (e) {
/* 10  */         formToFadeOut = $('form#register');
/* 11  */         formtoFadeIn = $('form#login');
/* 12  */         if ($(this).attr('id') == 'pop_signup') {
/* 13  */             formToFadeOut = $('form#login');
/* 14  */             formtoFadeIn = $('form#register');
/* 15  */         }
/* 16  */         formToFadeOut.fadeOut(500, function () {
/* 17  */             formtoFadeIn.fadeIn();
/* 18  */         })
/* 19  */         return false;
/* 20  */     });
/* 21  */ 	// Close popup
/* 22  */     $(document).on('click', '.login_overlay, .close', function () {
/* 23  */ 		$('form#login, form#register , form#forgot_password_form').fadeOut(500, function () {
/* 24  */             $('.login_overlay').remove();
/* 25  */         });
/* 26  */         return false;
/* 27  */     });
/* 28  */    
/* 29  */ 
/* 30  */     // Show the login/signup popup on click
/* 31  */     $('#show_login, #show_signup, .show-register').on('click', function (e) {
/* 32  */         $('body').prepend('<div class="login_overlay"></div>');
/* 33  */         if ($(this).attr('id') == 'show_login') 
/* 34  */ 			$('form#login').fadeIn(500);
/* 35  */         else 
/* 36  */ 			$('form#register').fadeIn(500);
/* 37  */         e.preventDefault();
/* 38  */     });
/* 39  */ 
/* 40  */ 	// Perform AJAX login/register on form submit
/* 41  */ 	$('form#login, form#register').on('submit', function (e) {
/* 42  */ 	
/* 43  */         if (!$(this).valid()) return false;
/* 44  */         $('p.status', this).show().text(ajax_auth_object.loadingmessage);
/* 45  */ 		action = 'ajaxlogin';
/* 46  */ 		username = 	$('form#login #username').val();
/* 47  */ 		password = $('form#login #password').val();
/* 48  */ 		email = '';
/* 49  */ 		security = $('form#login #security').val();
/* 50  */ 		blog_id	=	0; 

/* ajax-auth-script.js */

/* 51  */ 		if ($(this).attr('id') == 'register') {
/* 52  */ 			action = 'ajaxregister';
/* 53  */ 			username = $('#signonname').val();
/* 54  */ 			password = $('#signonpassword').val();
/* 55  */         	email = $('#email').val();
/* 56  */         	security = $('#signonsecurity').val();	
/* 57  */         	blog_id = $('#x_church_name').val();	
/* 58  */         	password2 = $('#password2').val();	
/* 59  */ 			if(password!=password2){ $('p.status', this).show().text("Password should be same");return false;}
/* 60  */ 		}  
/* 61  */ 		ctrl = $(this);
/* 62  */ 		$.ajax({
/* 63  */             type: 'POST',
/* 64  */             dataType: 'json',
/* 65  */             url: ajax_auth_object.ajaxurl,
/* 66  */             data: {
/* 67  */                 'action': action,
/* 68  */                 'username': username,
/* 69  */                 'password': password,
/* 70  */ 				'email': email,
/* 71  */ 				'blog_id': blog_id,
/* 72  */                 'security': security
/* 73  */             },
/* 74  */             success: function (data) {
/* 75  */ 				$('p.status', ctrl).text(data.message);
/* 76  */ 				if (data.loggedin == true) {
/* 77  */                     document.location.href = ajax_auth_object.redirecturl;
/* 78  */                 }
/* 79  */             }
/* 80  */         });
/* 81  */         e.preventDefault();
/* 82  */     });
/* 83  */ 	
/* 84  */ 	// Client side form validation
/* 85  */     if (jQuery("#register").length) {
/* 86  */ 		//jQuery("#register").validate();
/* 87  */ 		
/* 88  */ 		$("#register").validate({
/* 89  */ 			rules: {
/* 90  */ 				signonpassword: {
/* 91  */ 					required: true,
/* 92  */ 					minlength: 5
/* 93  */ 				},
/* 94  */ 				password2: {
/* 95  */ 					required: true,
/* 96  */ 					minlength: 5,
/* 97  */ 					equalTo: "#signonpassword"
/* 98  */ 				},
/* 99  */ 			},
/* 100 */ 			messages: {

/* ajax-auth-script.js */

/* 101 */ 				signonpassword: {
/* 102 */ 					required: "Please provide a password",
/* 103 */ 					minlength: "Your password must be at least 5 characters long"
/* 104 */ 				},
/* 105 */ 				password2: {
/* 106 */ 					required: "Please provide a password",
/* 107 */ 					minlength: "Your password must be at least 5 characters long",
/* 108 */ 					equalTo: "Please enter same password as in password field"
/* 109 */ 				},
/* 110 */ 			}
/* 111 */ 		});
/* 112 */ 		}
/* 113 */     else if (jQuery("#login").length) {
/* 114 */ 		//jQuery("#login").validate();
/* 115 */ 		
/* 116 */ 		$("#login").validate({
/* 117 */ 			rules: {
/* 118 */ 				username: "required",
/* 119 */ 				password: "required",
/* 120 */ 
/* 121 */ 			},
/* 122 */ 			messages: {
/* 123 */ 				username: "Please enter your username",
/* 124 */ 				password: "Please enter your password",
/* 125 */ 			}
/* 126 */ 		});
/* 127 */ 		}
/* 128 */ 		
/* 129 */ });

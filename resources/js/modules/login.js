var Login = {};


//--------------
// Variable
//--------------

Login.passwordField = '.js-password-field';
Login.seePwd = '.js-see-pwd';
Login.field = '.js-login-form .form-field';
Login.submitButton = '.js-login-button';


/**
 * Login function
 * @return {void}
 */
Login.seePassword = function() {
	// Active icon
	$(Login.seePwd).toggleClass('is-active');

	// Display/Hide password text
	if($(Login.passwordField).attr('type') == 'password') {
		$(Login.passwordField).attr('type', 'text');
	} else {
		$(Login.passwordField).attr('type', 'password');
	}
}


/**
 * Login - Submit button
 * @return {void}
 */
Login.submitBtnStatus = function() {
	$(Login.field).keyup(function() {
    var empty = false;
    $(Login.field).each(function() {
      if ($(this).val() == '') {
        empty = true;
      }
    });

    if (empty) {
    	// Disabled
      $(Login.submitButton).attr('disabled', true);
    } else {
    	// Enabled
      $(Login.submitButton).attr('disabled', false);
    }
  });
}

//--------------
// Execution
//--------------

Login.init = function(){
	// See password
	$(Login.seePwd).click(function(){
		Login.seePassword();
	});

	// Submit Button status
	Login.submitBtnStatus();
}

Login.init();

module.exports = Login;

var Darkmode = {};


//--------------
// Variable
//--------------

Darkmode.timer = null;



//--------------
// Function
//--------------

/**
 * Darkmode switch
 * @return {void}
 */
Darkmode.switch = function(event){
	$('body').toggleClass('theme-light');
	$('body').toggleClass('theme-dark');

	if (Boolean(localStorage.getItem('is-dark-mode'))) {
		localStorage.removeItem('is-dark-mode', '');
	} else {
		localStorage.setItem('is-dark-mode', '1');
	}

	event.preventDefault();
}


//--------------
// Execution
//--------------


$('.js-dark-mode-button').click(function(event) {
	Darkmode.switch(event);

	$(this).find('.switch-button').toggleClass('is-active');
});

if (Boolean(localStorage.getItem('is-dark-mode'))) {
	$('body').removeClass('theme-light').addClass('theme-dark');
	$('.switch-button').addClass('is-active');
} else {
	$('body').removeClass('theme-dark');
}

module.exports = Darkmode;




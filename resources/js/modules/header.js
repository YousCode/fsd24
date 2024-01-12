var Header = {};


//--------------
// Variable
//--------------

Header.variable = '';


/**
 * Header function
 * @return {void}
 */
Header.myFunction = function() {
	console.log('header js');
}


//--------------
// Execution
//--------------

Header.init = function(){
	//-- Social Networks Display
	Header.myFunction();

}

Header.init();

module.exports = Header;

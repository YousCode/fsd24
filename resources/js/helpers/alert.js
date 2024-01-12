// Alert Helper

const Alert = {
  install(Vue, options) {
  	Vue.prototype.Alert = {};

    Vue.prototype.Alert._defaultClose = (method) => {
    	if(window.changeWhenPopupIsOpen){
	    	Vue.prototype.$bus.$emit('CONFIRM', {
	  			title: 'Attention',
	  			text: 'Vous êtes sur le point de quitter la « Création de projet ».  Si vous quittez, vos modifications ne seront pas enregistrées.',
	  			confirmText: 'Êtes-vous sûr de vouloir quitter ?',
	  			button_1: {
	  				title: 'Quitter sans enregistrer',
	  				class: 'test',
	  				method: method
	  			},
	  			button_2: {
	  				title: 'Annuler'
	  			}
	  		});
	  	} else {
	  		method();
	  	}
    };

  },
}

Vue.use(Alert);

// Talent Helper

const Talent = {
  install(Vue, options) {
  	Vue.prototype.Talent = {};

    Vue.prototype.Talent._edit = (talent) => {
      Vue.prototype.$bus.$emit('ACTION_CHANGED', {
  	 		action: 'SET_TALENT',
  	 		talent: talent
  	 	});
    }

    Vue.prototype.Talent._delete = (talent, callback = null, args = []) => {
      Vue.prototype.$bus.$emit('CONFIRM', {
  			title: 'Attention',
  			text: 'Voulez-vous supprimer ce talent ?',
  			confirmText: 'Êtes vous sûr de vouloir supprimer ce contact ?',
  			button_1: {
  				title: 'Supprimer',
  				class: '',
  				method: Vue.prototype.Talent._deleteConfirm,
  				args: { talent, callback, args }
  			},
  			button_2: {
  				title: 'Annuler',
  			}
  		});
    }

    /**
     * [description]
     * @param  {[type]}   params.talent   [description]
     * @param  {Function} params.callback [description]
     * @param  {Array}    params.args   [description]
     * @return {[type]}            [description]
     */
    Vue.prototype.Talent._deleteConfirm = (params = {}) => {
      axios.delete("/api/talent/" + params.talent.id)
			.then(res => {
  			if(res.success === false){
      		// Todo : Error
      	} else {
      		if(params.callback){
      			params.callback(params.args.join(','));
      		}
      	}
      }).catch(error => console.log(error));
    }
  },
}

Vue.use(Talent);
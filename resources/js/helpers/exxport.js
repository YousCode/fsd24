// Export Helper

const Exxport = {
  install(Vue, options) {
  	Vue.prototype.Exxport = {};

    Vue.prototype.Exxport._delete = (exxports, callback = null, args = []) => {
    	  let exxportToDelete = [];
          for (const index in exxports) {
              if (exxports[index].delete) {
                  exxportToDelete.push(exxports[index].delete);
			        }
              if (exxports[index].id) {
                exxportToDelete.push(exxports[index].id);
            }
        }
        Vue.prototype.$bus.$emit('CONFIRM', {
  			title: 'Attention',
  			text: 'Voulez-vous supprimer ce(s) livrable(s) ?',
  			confirmText: 'Êtes-vous sûr de bien vouloir supprimer cet élément ?',
  			button_1: {
  				title: 'Supprimer',
  				class: '',
  				method: Vue.prototype.Exxport._deleteConfirm,
  				args: { exxportToDelete, callback, args }
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
    Vue.prototype.Exxport._deleteConfirm = (params = {}) => {
      axios.delete("/api/export/", {
      	data: {
      		exports: params.exxportToDelete
      	}
      })
			.then(res => {
  			if(res.success === false){
      		// Todo : Error
          } else {
      		if(params.callback){
      			params.callback(params.args.join(','));
      		}
      		Vue.prototype.$bus.$emit('EXPORT_ADD_OR_UPDATE');
          Vue.prototype.$bus.$emit('ACTION_CHANGED', {
            action: 'CONGRATS',
            type: 'DELETE_EXXPORT'
          });
      	}
      }).catch(error => console.log(error));
    },
      /**
       * [description]
       * @param  {[type]}   params.talent   [description]
       * @param  {Function} params.callback [description]
       * @param  {Array}    params.args   [description]
       * @return {[type]}            [description]
       */
      Vue.prototype.Exxport._deleteOne = (exxport = {}, exxports = {}, key = null) => {
      	  var exportId = [];
      	  if (exxport.id) {
              exportId.push(exxport.id);
              axios.delete("/api/export/", {
                  data: {
                      exports: exportId
                  }
              })
                  .then(res => {
                  if(res.success === false){
                  // Todo : Error
              } else {
                  Vue.prototype.$bus.$emit('EXPORT_ADD_OR_UPDATE');
              }
          }).catch(error => console.log(error));
		 } else {
      	  	  if (key) {
      	  	  	this.$delete(exxports, key);
      	  	  	Vue.prototype.$bus.$emit('EXPORT_ADD_OR_UPDATE');
              }
		 }
      }
  },
}

Vue.use(Exxport);
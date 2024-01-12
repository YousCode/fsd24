// File Helper

const File = {
  install(Vue, options) {
  	Vue.prototype.File = {};

    Vue.prototype.File._delete = (files, callback = null, args = []) => {
      Vue.prototype.$bus.$emit('CONFIRM', {
  			title: 'Attention',
  			text: 'Voulez-vous supprimer ce(s) fichier(s) ?',
  			confirmText: 'Voulez-vous supprimer ce(s) fichier(s) ?',
  			button_1: {
  				title: 'Supprimer',
  				class: '',
  				method: Vue.prototype.File._deleteConfirm,
  				args: { files, callback, args }
  			},
  			button_2: {
  				title: 'Annuler',
  			}
  		});
    }

    Vue.prototype.File._deleteConfirm = (params = {}) => {
      axios.delete("/api/file/", {
      	params: {
      		files: params.files
      	}
      })
			.then(res => {
  			if(res.success === false){
      		// Todo : Error
      	} else {
      		if(params.callback){
      			params.callback(params.args.join(','));
      		}
      		Vue.prototype.$bus.$emit('FILE_ADD_OR_UPDATE');
          Vue.prototype.$bus.$emit('UPDATE_COUNT_MESSAGES', 0);
          Vue.prototype.$bus.$emit('ACTION_CHANGED', {
            action: 'CONGRATS',
            type: 'DELETE_MEDIA'
          });
      	}
      }).catch(error => console.log(error));
    },
      Vue.prototype.File._deleteOne = (file = {}) => {
          var fileId = [];
          fileId.push(file.id);
          axios.delete("/api/file/", {
              data: {
                  files: fileId
              }
          })
              .then(res => {
              if(res.success === false){
              // Todo : Error
          } else {
              Vue.prototype.$bus.$emit('FILE_ADD_OR_UPDATE');
          }
      }).catch(error => console.log(error));
      }
  },
}

Vue.use(File);


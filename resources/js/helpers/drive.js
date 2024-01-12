// Drive Helper

const Drive = {
    install(Vue, options) {
        Vue.prototype.Drive = {};
  
      Vue.prototype.Drive._delete = (drives, callback = null, args = []) => {
        Vue.prototype.$bus.$emit('CONFIRM', {
                title: 'Attention',
                text: 'Voulez-vous supprimer ce lien ?',
                confirmText: 'Voulez-vous supprimer ce lien ?',
                button_1: {
                    title: 'Supprimer',
                    class: '',
                    method: Vue.prototype.Drive._deleteConfirm,
                    args: { drives, callback, args }
                },
                button_2: {
                    title: 'Annuler',
                }
        });
      }
  
      Vue.prototype.Drive._deleteConfirm = (params = {}) => {
        axios.delete("/api/drive/", {
            params: {
                drives: params.drives
            }
        })
              .then(res => {
                if(res.success === false){
                // Todo : Error
            } else {
                if(params.callback){
                    params.callback(params.args.join(','));
                }
                Vue.prototype.$bus.$emit('UPDATE_DRIVE');
                Vue.prototype.$bus.$emit('ACTION_CHANGED', {
                    action: 'CONGRATS',
                    type: 'DELETE_DRIVE'
                });
            }
        }).catch(error => console.log(error));
      },
        Vue.prototype.Drive._deleteOne = (drive = {}) => {
            var driveId = [];
            driveId.push(drive.id);
            axios.delete("/api/drive/", {
                data: {
                    drives: driveId
                }
            })
                .then(res => {
                if(res.success === false){
                // Todo : Error
            } else {
                //Vue.prototype.$bus.$emit('FILE_ADD_OR_UPDATE');
            }
        }).catch(error => console.log(error));
        }
    },
  }
  
  Vue.use(Drive);
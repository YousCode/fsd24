const Portfolios = {
    install(Vue, options) {
        Vue.prototype.Portfolios = {};

        Vue.prototype.Portfolios._delete = (portfolios, callback = null, args = []) => {
            Vue.prototype.$bus.$emit('CONFIRM', {
                title: 'Attention',
                text: 'Voulez-vous supprimer ce projet ?',
                confirmText: 'Voulez-vous supprimer ce projet ?',
                button_1: {
                    title: 'Supprimer',
                    class: '',
                    method: Vue.prototype.Portfolios._deleteConfirm,
                    args: { portfolios, callback, args }
                },
                button_2: {
                    title: 'Annuler',
                }
            });
        }

        Vue.prototype.Portfolios._deleteConfirm = (params = {}) => {
            axios.delete("/api/explorer/portfolios/"+ params.portfolios, {
            }).then(res => {
                    if(res.success === false){
                        // Todo : Error
                    } else {
                        if(params.callback){
                            params.callback(params.args.join(','));
                        }
                        Vue.prototype.$bus.$emit('deleteProject');
                        Vue.prototype.$bus.$emit('ACTION_CHANGED', {
                            action: 'CONGRATS',
                            type: 'DELETE_PORTFOLIO'
                        });
                    }
                }).catch(error => console.log(error));
        },
            Vue.prototype.Drive._deleteOne = (portfolio = {}) => {
                let portfolioId = [];
                portfolioId.push(portfolio.id);
                axios.delete("/api/explorer/portfolios/{portfolio}", {
                    data: {
                        portfolios: portfolioId
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

Vue.use(Portfolios);

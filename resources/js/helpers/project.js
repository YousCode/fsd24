// Project Helper

const Project = {
  install(Vue, options) {
  	Vue.prototype.Project = {};

  	Vue.prototype.Project._goTo = (project_id) => {
      window.location.href = '/projects/' + project_id;
    }

    Vue.prototype.Project.get = (project_id) => {
        axios.get('/api/project/' + project_id)
        .then(res => {
            if (typeof res.data.datas !== "undefined") {
                let project = res.data.datas;
                return project;
            } else {
                //error
            }
        }).catch(error => console.log(error));
    }

    Vue.prototype.Project._edit = (project) => {
      Vue.prototype.$bus.$emit('ACTION_CHANGED', {
  	 		action: 'SET_PROJECT',
  	 		project: project
  	 	});
    }

    Vue.prototype.Project._delete = (project, callback = null, args = []) => {
      Vue.prototype.$bus.$emit('CONFIRM', {
  			confirmText: 'Êtes-vous sûr de vouloir supprimer ce projet ?',
  			button_1: {
  				title: "Supprimer",
  				class: '',
  				method: Vue.prototype.Project._deleteConfirm,
  				args: { project, callback, args }
  			},
  			button_2: {
  				title: "Annuler",
  			}
  		});
    },

    Vue.prototype.Project._deleteConfirm = (params = {}) => {
    	axios.delete('/api/project/' + params.project.id)
			.then(res => {
  			if(res.success === false){
      		// Todo : Error
      	} else {
      		if(params.callback){
      			params.callback(params.args.join(','));
      		}
			window.location.href = "/projects";
      	}
      }).catch(error => console.log(error));
    }
    Vue.prototype.Project._confirmClosed = (params = {}) => {
        Vue.prototype.$bus.$emit("UPDATE_PROJECT_LOAD", {isLoaded: true});
        if (params && params.from && params.to && params.projectID) {
            axios.post("/api/project/update", {from: params.from, to: params.to, projectId: params.projectID}).then(res => {
                if (res && res.data && res.data.projectId && res.data.projectId > 0) {
                    Vue.prototype.$bus.$emit("ACTION_CHANGED", { action: "CONGRATS", type: 'PROJECT_MOVED'});
                    Vue.prototype.$bus.$emit("UPDATE_PROJECT_STEP", {projectId: Number(res.data.projectId)});
                }
            }).catch(error => console.log(error));
        }
    }
    Vue.prototype.Project._addTask = (params = {}) => {
        if (params && params.projectID) {
            axios.get("/api/project/" + params.projectID, {}).then(res => {
                if (res && res.data && res.data.datas) {
                    let project = res.data.datas;
                    Vue.prototype.Global._setAction("SET_TASK", {project: project, type: 'ADD', formType: 'projects'});
                }
            }).catch(error => console.log(error));
        }
    }
  },
}

Vue.use(Project);


const Explorer = {
    install(Vue, options) {
        Vue.prototype.Explorer = {};

        Vue.prototype.Explorer._goback = () => {
            window.location.href = "/explorer/messenger";
        }
        Vue.prototype.Explorer._confirm = (callback = null, args = []) => {
            Vue.prototype.$bus.$emit('CONFIRM', {
                confirmText: 'Êtes-vous sûr de quitter ?',
                button_1: {
                    title: "Revenir en arrière",
                    class: '',
                    args: {callback, args }
                },
                button_2: {
                    title: "Quitter",
                    method: Vue.prototype.Explorer._goback,
                }
            });
        }
    }
}
Vue.use(Explorer);

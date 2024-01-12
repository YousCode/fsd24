// Task Helper
import moment from 'moment'

const { before } = require("lodash");

const Task = {
    install(Vue, options) {
        Vue.prototype.Task = {};
        Vue.prototype.Task._get = (projetId) => {
            axios.get('/api/project/' + projetId).then(res => {
                if (res.success === false) {
                    // Todo : Error
                } else {
                    Vue.prototype.$bus.$emit('PLANNING_UPDATE', res.data.datas.task);
                }
            }).catch(error => console.log(error));
        }

        Vue.prototype.Task._edit = (task) => {
            Vue.prototype.$bus.$emit('ACTION_CHANGED', {
                action: 'SET_TASK_SINGLE',
                task: task
            });
        }

        Vue.prototype.Task._delete = (task, project, callback, args = []) => {
            Vue.prototype.$bus.$emit('CONFIRM', {
                title: 'Attention',
                text: 'Êtes-vous sûr de vouloir supprimer cette tâche ?',
                confirmText: 'Êtes-vous sûr de vouloir supprimer cette tâche ?',
                button_1: {
                    title: 'Supprimer',
                    class: '',
                    method: Vue.prototype.Task._deleteConfirm,
                    args: { task, project, callback, args }
                },
                button_2: {
                    title: 'Revenir en arrière',
                }
            });
        }

        Vue.prototype.Task._deleteConfirm = (params = {}) => {
            if (params.task) {
                /*let deleteTaskRoute = false;
                if (params.args.deleteParentTask) {*/
                let deleteTaskRoute = "/api/parent/task/" + params.task;
                /*} else {
                    deleteTaskRoute = "/api/task/" + params.task;
                }*/
                axios.delete(deleteTaskRoute)
                .then(res => {
                    if (res.success === false) {
                        // Todo : Error
                    } else {
                        /*if (params.callback) {
                            params.callback(params.args.join(','));
                        }*/
                        Vue.prototype.$bus.$emit('TASK_ADD_OR_UPDATE', res.data); // Emit add or update talent event
                        Vue.prototype.$bus.$emit('ACTION_CHANGED', {
                            action: 'CONGRATS',
                            type: 'DELETE_TASK'
                        });
                        Vue.prototype.$bus.$emit('PLANNING_TO_SCROLL');
                        let projectId = false;
                        if (typeof params.project !== "undefined" && params.project && params.project.id) {
                            projectId = params.project.id
                        } else if (params.args && params.args.projectId) {
                            projectId = params.args.projectId
                        }
                        if (projectId) {
                            Vue.prototype.Task._get(projectId);
                        }
                    }
                }).catch(error => console.log(error));
            }
        }
        Vue.prototype.Task._close = (task, daily = false) => {
            let args = {task: task};
            if (daily) {
                args.daily = daily;
            }
            Vue.prototype.$bus.$emit('CONFIRM', {
                action: 'TASK_OVER',
                title: 'Attention',
                text: 'Voulez-vous clôturer cette tâche ?',
                confirmText: 'Êtes vous sûr de vouloir clôturer cette tâche ?',
                button_1: {
                    title: 'Clôturer',
                    class: '',
                    method: Vue.prototype.Task._confirmeClose,
                    args: args
                },
                button_2: {
                    title: 'Annuler',
                }
            });

            if (document.getElementsByClassName('confirm-popup') && document.getElementsByClassName('confirm-popup')[0] && document.getElementsByClassName('confirm-popup')[0].style.top !== '') {
                let popup = document.getElementsByClassName('confirm-popup')[0];
                popup.style.top = '';
                popup.style.left = '';
            }
        }

        Vue.prototype.Task._confirmeClose = (params = {}) => {
            let taskId = (params.task.task_id) ? params.task.task_id : params.task.id;
            let isDaily = (params.daily) ? true : false;
            axios.patch('/api/task/' + taskId, {
                    action: 'set_closed',
                })
                .then(res => {
                    if (res.success === false) {
                        // Todo : Error
                    } else {
                        Vue.prototype.$bus.$emit("ACTION_CHANGED", {
                            action: "CONGRATS",
                            type: "TASK_CLOSE"
                        }); // Congrats modal
                        Vue.prototype.$bus.$emit('TASK_ADD_OR_UPDATE', res.data);
                        let projectId = false;
                        if (params.task && params.task.project_id) {
                            projectId = params.task.project_id;
                        }
                        if (projectId) {
                            Vue.prototype.Task._get(projectId);
                        }
                        if (isDaily) {
                            Vue.prototype.$bus.$emit('RESET_TASK_TO_CLOSE');
                        }
                    }
                    //document.querySelector('[data-id="' + params.task.id + '"]').lastChild.innerHTML = "Clôturé";
                }).catch(error => console.log(error));
        }

        Vue.prototype.Task._setAccepted = (task, value, callback = null, args = []) => {
            axios.patch('/api/task/' + task.id, {
                    action: 'set_acceptation',
                    value: value
                })
                .then((res) => {
                    if (res.success === false) {
                        // Todo : Error
                    } else {
                        if (callback) {
                            callback(args.join(','));
                        }
                    }
                })
                .catch(error => console.log(error));
        };

    },

}

Vue.use(Task);
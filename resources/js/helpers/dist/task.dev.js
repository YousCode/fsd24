"use strict";

var _moment = _interopRequireDefault(require("moment"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

// Task Helper
var _require = require("lodash"),
    before = _require.before;

var Task = {
  install: function install(Vue, options) {
    Vue.prototype.Task = {};

    Vue.prototype.Task._get = function (projetId) {
      axios.get('/api/project/' + projetId).then(function (res) {
        if (res.success === false) {// Todo : Error
        } else {
          Vue.prototype.$bus.$emit('PLANNING_UPDATE', res.data.datas.task);
        }
      })["catch"](function (error) {
        return console.log(error);
      });
    };

    Vue.prototype.Task._edit = function (task) {
      Vue.prototype.$bus.$emit('ACTION_CHANGED', {
        action: 'SET_TASK_SINGLE',
        task: task
      });
    };

    Vue.prototype.Task._delete = function (task, project, callback) {
      var args = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : [];
      Vue.prototype.$bus.$emit('CONFIRM', {
        title: 'Attention',
        text: 'Êtes-vous sûr de vouloir supprimer cette tâche ?',
        confirmText: 'Êtes-vous sûr de vouloir supprimer cette tâche ?',
        button_1: {
          title: 'Supprimer',
          "class": '',
          method: Vue.prototype.Task._deleteConfirm,
          args: {
            task: task,
            project: project,
            callback: callback,
            args: args
          }
        },
        button_2: {
          title: 'Revenir en arrière'
        }
      });
    };

    Vue.prototype.Task._deleteConfirm = function () {
      var params = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      axios["delete"]("/api/task/" + params.task.id).then(function (res) {
        if (res.success === false) {// Todo : Error
        } else {
          if (params.callback) {
            params.callback(params.args.join(','));
          }

          Vue.prototype.$bus.$emit('TASK_ADD_OR_UPDATE', res.data); // Emit add or update talent event

          Vue.prototype.$bus.$emit('ACTION_CHANGED', {
            action: 'CONGRATS',
            type: 'DELETE_TASK'
          });
          Vue.prototype.$bus.$emit('PLANNING_TO_SCROLL');

          if (params.project.id) {
            Vue.prototype.Task._get(params.project.id);
          }
        }
      })["catch"](function (error) {
        return console.log(error);
      });
    };

    Vue.prototype.Task._close = function (task, callback) {
      var args = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : [];
      Vue.prototype.$bus.$emit('CONFIRM', {
        title: 'Attention',
        text: 'Voulez-vous clôturer cette tâche ?',
        confirmText: 'Êtes vous sûr de vouloir clôturer cette tâche?',
        button_1: {
          title: 'Clôturer',
          "class": '',
          method: Vue.prototype.Task._confirmeClose,
          args: {
            task: task,
            callback: callback,
            args: args
          }
        },
        button_2: {
          title: 'Annuler'
        }
      });
    };

    Vue.prototype.Task._confirmeClose = function () {
      var params = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      axios.patch('/api/task/' + params.task.id, {
        action: 'set_closed'
      }).then(function (res) {
        if (res.success === false) {// Todo : Error
        } else {
          if (params.callback) {
            params.callback(params.args.join(','));
          }

          Vue.prototype.$bus.$emit("ACTION_CHANGED", {
            action: "CONGRATS",
            type: "TASK_CLOSE"
          }); // Congrats modal 

          Vue.prototype.$bus.$emit('TASK_ADD_OR_UPDATE', res.data);
        }

        document.querySelector('[data-id="' + params.task.id + '"]').lastChild.innerHTML = "Clôturé";
      })["catch"](function (error) {
        return console.log(error);
      });
    };

    Vue.prototype.Task._setAccepted = function (task, value) {
      var callback = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
      var args = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : [];
      axios.patch('/api/task/' + task.id, {
        action: 'set_acceptation',
        value: value
      }).then(function (res) {
        if (res.success === false) {// Todo : Error
        } else {
          if (callback) {
            callback(args.join(','));
          }
        }
      })["catch"](function (error) {
        return console.log(error);
      });
    };
  }
};
Vue.use(Task);
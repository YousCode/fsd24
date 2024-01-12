"use strict";

// Appointment Helper
var _require = require("lodash"),
    before = _require.before;

var Appointment = {
  install: function install(Vue, options) {
    Vue.prototype.Appointment = {};

    Vue.prototype.Appointment._edit = function (appointment) {
      Vue.prototype.$bus.$emit('ACTION_CHANGED', {
        action: 'SET_APPOINTMENTFORM',
        appointment: appointment
      });
    };

    Vue.prototype.Appointment._delete = function (appointment, callback) {
      var args = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : [];
      Vue.prototype.$bus.$emit('CONFIRM', {
        title: 'Attention',
        text: '',
        confirmText: 'Voulez-vous supprimer ce rendez-vous ?',
        button_1: {
          title: 'Supprimer',
          "class": '',
          method: Vue.prototype.Appointment._deleteConfirm,
          args: {
            appointment: appointment,
            callback: callback,
            args: args
          }
        },
        button_2: {
          title: 'Annuler'
        }
      });
    };

    Vue.prototype.Appointment._deleteConfirm = function () {
      var params = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      axios["delete"]("/api/appointment/" + params.appointment.id).then(function (res) {
        if (res.success === false) {// Todo : Error
        } else {
          if (params.callback) {
            params.callback(params.args.join(','));
            Vue.prototype.$bus.$emit("APPOINTMENT", res.data); // Emit add or update appointment event

            Vue.prototype.$bus.$emit("ACTION_CHANGED", {
              action: "CONGRATS",
              type: "APPOINTMENT_DELETE"
            }); // Congrats modal 
          }
        }
      })["catch"](function (error) {
        return console.log(error);
      });
    };
  }
};
Vue.use(Appointment);
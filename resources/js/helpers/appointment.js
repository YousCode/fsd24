// Appointment Helper
const { before } = require("lodash");

const Appointment = {
    install(Vue, options) {
        Vue.prototype.Appointment = {};

        Vue.prototype.Appointment._edit = (appointment) => {
            Vue.prototype.$bus.$emit('ACTION_CHANGED', {
                action: 'SET_APPOINTMENTFORM',
                appointment: appointment,

            });
        }

        Vue.prototype.Appointment._delete = (appointment, callback, args = []) => {
            Vue.prototype.$bus.$emit('CONFIRM', {
                title: 'Attention',
                text: '',
                confirmText: 'Voulez-vous supprimer ce rendez-vous ?',
                button_1: {
                    title: 'Supprimer',
                    class: '',
                    method: Vue.prototype.Appointment._deleteConfirm,
                    args: { appointment, callback, args }
                },
                button_2: {
                    title: 'Annuler',
                }
            });
        }

        Vue.prototype.Appointment._deleteConfirm = (params = {}) => {
            axios.delete("/api/appointment/" + params.appointment.id)
                .then(res => {
                    if (res.success === false) {
                        // Todo : Error
                    } else {
                        if (params.callback) {
                            params.callback(params.args.join(','));
                        }
                            Vue.prototype.$bus.$emit("APPOINTMENT", res.data); // Emit add or update appointment event
                            Vue.prototype.$bus.$emit('APPOINTMENT_ADD_OR_UPDATE');
                            Vue.prototype.$bus.$emit("ACTION_CHANGED", {
                                action: "CONGRATS",
                                type: "APPOINTMENT_DELETE"
                            }); // Congrats modal 
                    }
                }).catch(error => console.log(error));
        }
    },
}

Vue.use(Appointment);
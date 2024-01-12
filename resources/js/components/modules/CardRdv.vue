<template>
    <div
        class="block has-bg rdv__block block-g"
        style="box-shadow: 0px 0px 15px #120b23"
    >
        <div style="margin-bottom: 10px; display: flex">
            <div class="main-appointment">
                Rendez-vous
                <div>
                    <button
                        class="tiny-button-section is-gradient"
                        @click="
                            Global._setAction('SET_APPOINTMENTFORM')
                        "
                    >
                        <span class="tiny-button icon-plus"></span>
                    </button>
                </div>
            </div>
            <div
                class="filter-select appointment-filters_notes"
                v-if="
                    todaysAppointments.length !== 0 || tomorrowsAppointments.length !== 0
                "
            >
                <div :class="`c-1 ${activeTab === 'today' ? 'active':''}`" @click="changeTab('today')">
                    Aujourd'hui
                    <div :class="`badge-rdv` + (newTodayAppointment.length > 0 ? ' new': '')">
                        {{ todaysAppointments.length }}
                    </div>
                </div>
                <div :class="`c-1 ${activeTab === 'tomorrow' ? 'active':''}`" @click="changeTab('tomorrow')">
                    Demain
                    <div :class="`badge-rdv` + (newTomorrowAppointment.length > 0 ? ' new': '')">
                        {{ tomorrowsAppointments.length }}
                    </div>
                </div>
            </div>
        </div>

        <div v-if="!appointmentsList.length">
            <div class="no_appointment" @click="Global._setAction('SET_APPOINTMENTFORM')">
                <img src="images/work.png" alt="" style="width: 63px; height: 47px; margin: auto; position: absolute; top: 49%; left: 50%; transform: translate(-50%, -50%); -webkit-transform: translate(-50%, -50%); transform: translate3d(-50%, -50%, 0);"/>
                <div style="color: #7665a7; font-weight: 700; position: absolute; top: 55%; right: 43%;">
                    Ajouter un RDV
                </div>
            </div>
        </div>

        <AppointmentsTab :appointments="appointmentsList" :contextMenu="contextMenu" :contextMenuCallback="contextMenuCallback" :user="user" v-else />
    </div>
</template>

<script>
import AppointmentsTab from '../appointments/AppointmentsTab.vue';

export default {
    components: {
        AppointmentsTab,
    },

    props: [
        "appointments",
        "contextMenu",
        'contextMenuCallback',
        'user'
    ],

    data() {
        return {
            activeTab: 'today',
            newAppointmentTab: null,
            newTodayAppointment: [],
            newTomorrowAppointment: [],
        };
    },
    computed: {
        appointmentsList() {
            return this.appointments.filter((appointment) => this.getDateInformation(appointment.datetime) === this.activeTab);
        },
        todaysAppointments() {
            return this.appointments.filter((appointment) => this.getDateInformation(appointment.datetime) === 'today');
        },
        tomorrowsAppointments() {
            return this.appointments.filter((appointment) => this.getDateInformation(appointment.datetime) === 'tomorrow');
        },
    },
    mounted() {
        this.$bus.$on("APPOINTMENT_ADDED", data => {
            let appointment = this.appointments.find((appointment) => appointment.id === data.appointment_id);
            let appointmentId = data.appointment_id;
            if (this.getDateInformation(appointment.datetime) === 'today') {
                this.newAppointmentTab = 'today';
                this.newTodayAppointment.push(appointmentId);
            } else {
                this.newAppointmentTab = 'tomorrow';
                this.newTomorrowAppointment.push(appointmentId);
            }
            
        });

        for (const appointment of this.todaysAppointments) {
            if (appointment.unviewedUser && appointment.unviewedUser.includes(this.user.id)) {
                this.newTodayAppointment.push(appointment.id);
            }
        }
        for (const appointment of this.tomorrowsAppointments) {
            if (appointment.unviewedUser && appointment.unviewedUser.includes(this.user.id)) {
                this.newTomorrowAppointment.push(appointment.id);
            }
        }

        this.$bus.$on("APPOINTMENT_UPDATE", appointmentId => {
            if (this.newTodayAppointment.includes(appointmentId)) {
                let indexOf = this.newTodayAppointment.indexOf(appointmentId);
                this.newTodayAppointment.splice(indexOf, 1);
            }

            if (this.newTomorrowAppointment.includes(appointmentId)) {
                let indexOf = this.newTomorrowAppointment.indexOf(appointmentId);
                this.newTomorrowAppointment.splice(indexOf, 1);
            }
        });
    },
    methods: {
        changeTab(tabName) {
            this.activeTab = tabName;
        },
        getDateInformation(date) {
            const a = new Date(date);
            const aAsNumber = a.getFullYear() + a.getMonth() + a.getDate(); // 20230423
            const today = new Date();
            const todayAsNumber = today.getFullYear() + today.getMonth() + today.getDate(); // 20230324

            if (aAsNumber === todayAsNumber) {
                return 'today';
            } else if (aAsNumber < todayAsNumber) {
                return 'past';
            }

            return 'tomorrow';
        }
    },
};
</script>

<style lang="scss">
// @todo: find a better way that overwritting it
div.avatar-user-list {
    margin-left: 1rem; // same as the title
    margin-top:2rem;

    ul li .img-rdv-dash {
        margin-top: 0;
    }
}

.active {
    background-color: #150C2D;
    color: white;
}

.c-1 {
    margin-right: 1rem;
}

.badge-rdv {
    position: absolute;
    width: 19px;
    height: 15px;
    top: -8px;
    right: -8px;
    background: #7665A7;
    border-radius: 3px;
    text-align: center;
    color: #2B1C56;
    font-size: 9px;
    line-height: 9px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: bold;

    &.new {
        background: #FF003D;
        color: white;
    }
}

.main-appointment {
    text-transform: unset;
}

.scrollable {
    max-height: 550px;
    overflow: auto;
}

.no_appointment { 
    background-color: #1e1438;
    height: 550px;
    border-radius: 5px;
    margin: 10px;
    cursor: pointer;
}
</style>
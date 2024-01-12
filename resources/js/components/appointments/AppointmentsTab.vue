<template>
    <div class="plan-wrapper2">
        <div
            v-for="appointment in appointments"
            :id="'appointment-' + appointment.id"
            :class="`rdv__card`+(appointment.unviewedUser.includes(user.id) ? ' app-is-waiting' : '')"
            data-type="appointment"
            :key="appointment.id"
            @mouseenter.once="appointmentViewed(appointment.id, user.id, 'appointment-' + appointment.id, appointment.unviewedUser.includes(user.id))"
            >
            <div class="rdv-car_inf">
                <div class="rdv-marg">
                        <span class="rdv__clock"
                            ><span class="picto picto-time"></span>
                            {{ getHours(appointment.datetime) }}:{{
                                getMinutes(appointment.datetime)
                            }}</span
                        >
                        <div style="color: white; margin:1rem;">
                            <strong class="tooltiped">{{ appointment.title.slice(0, 45) }} {{ appointment.title.length > 45 ? '...':'' }}
                            <span class="tooltiptext title">{{ appointment.title }}</span>
                        </strong>
                        </div>
                        <div class="avatar-user-list" v-if="!isLoading">
                               <ul class="participants" v-for="(attendingParticipants, i) in participantsSplit(participants(appointment.talents, appointment.contacts), shouldDisplayAllParticipants(appointment.id) ? 10 : 6)" :key="`split-${i}`" v-show="i === 0 || shouldDisplayAllParticipants(appointment.id)">
                                    <li v-for="(participant, p_i) in attendingParticipants" :key="p_i" class="tooltiped">
                                        <span>
                                            <span v-if="participant.avatar">
                                                <img class="img-rdv-dash" v-bind:src="'/upload/avatars/' + participant.avatar" v-bind:alt="participant.name" v-if="'' !== participant.avatar"/>
                                                <div class="img-rdv-dash profile-register" v-else>
                                                    <div class="image_register">
                                                        <span class="user-initials">{{ getInitiales(participant)}}</span>
                                                    </div>
                                                </div>
                                                <span class="tooltiptext">{{ participant.name }}</span>
                                            </span>
                                            <span v-else>
                                                <div class="img-rdv-dash profile-register">
                                                    <div class="image_register">
                                                        <span class="user-initials">{{ getInitiales(participant, 'contact')}}</span>
                                                    </div>
                                                </div>
                                                <span class="tooltiptext">{{ participant.email }}</span>
                                            </span>
                                        </span>
                                    </li>
                                    <li v-if="participantsSplit(participants(appointment.talents, appointment.contacts), 6).length > 1 && i === (shouldDisplayAllParticipants(appointment.id) ? (participantsSplit(participants(appointment.talents, appointment.contacts), 10).length-1): 0)" class="tooltiped see-more-btn" @click="showHideParticipants(appointment.id)">
                                        <span>
                                            <div class="img-rdv-dash profile-register">
                                                <div class="image_register">
                                                    <span class="user-initials">...</span>
                                                </div>
                                            </div>
                                            <span class="tooltiptext" v-if="!shouldDisplayAllParticipants(appointment.id)">Afficher plus</span>
                                            <span class="tooltiptext" v-else>Afficher moins</span>
                                        </span>
                                    </li>
                               </ul>
                        </div>
                    </div>
                    <div
                        style="
                            display: flex;
                            align-items: center;
                            width: 60%;
                        "
                    >
                        <div class="rdv__contact">
                            <AppointmentDetails icon="location" :content="appointment.location" />
                            <AppointmentDetails icon="tel" linkType="tel" :content="appointment.talents[0].phone" />
                            <AppointmentDetails icon="mail" linkType="mailto" :content="appointment.talents[0].email" />
                        </div>
                        <div class="appointment-nav">
                            <div class="js-toggle-wrapper">
                                <div
                                    type="button"
                                    class="button appointment-edit"
                                    @click.prevent="
                                        contextMenu.show(
                                            $event,
                                            'APPOINTMENT',
                                            {
                                                appointment:
                                                    appointment,
                                            },
                                            contextMenuCallback,
                                            [true]
                                        )
                                    "
                                >
                                <!-- @todo: change it to a real icon w/ 3 dots -->
                                    <span
                                        class="dot-button-task"
                                    ></span>
                                    <span
                                        class="dot-button-task"
                                    ></span>
                                    <span
                                        class="dot-button-task"
                                    ></span>
                                </div>
                            </div>
                            <div class="js-position-wrapper">
                                <button
                                    type="button"
                                    class="action-button js-position-button appointment-notes_show"
                                    style="color: #7665a7"
                                    v-if="appointment.note"
                                >
                                    <span
                                        class="icon icon-notes icon-center"
                                    ></span>
                                </button>

                                <div class="notes js-position-content">
                                    <p class="text notes-color">
                                        {{ appointment.note }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</template>

<script>
import AppointmentDetails from '../appointments/AppointmentDetails.vue'

export default {
    components: {
        AppointmentDetails,
    },
    props: ['contextMenu', 'contextMenuCallback', 'appointments', 'user'],
    data() {
        return {
            newAppointmentId: null,
            displayParticipantsList: [],
            isLoading: true,
        }
    },
    mounted() {
        this.$bus.$on("APPOINTMENT_ADDED", data => {
            this.newAppointmentId = data.appointment_id;
        });

        this.appointments.forEach(a => {
            this.displayParticipantsList.push({
                show: false,
                appointment_id: a.id
            })
        })

        setTimeout(() => {
            this.isLoading = false;
        }, 500);
    },
    watch: {
        appointments: {
            handler() {
                this.displayParticipantsList = [];
                this.appointments.forEach(a => {
                    this.displayParticipantsList.push({
                        show: false,
                        appointment_id: a.id
                    })
                })
            }
        },
    },
    methods: {
        shouldDisplayAllParticipants(appointment_id) {
            let appointmentKey = (this.displayParticipantsList.findIndex(e => e.appointment_id === appointment_id));
            return this.displayParticipantsList[appointmentKey].show
        },
        showHideParticipants(appointment_id) {
            let appointmentKey = (this.displayParticipantsList.findIndex(e => e.appointment_id === appointment_id));
            this.displayParticipantsList[appointmentKey].show = !this.displayParticipantsList[appointmentKey].show
        },
        participants(talents, contacts) {
            return [...talents, ...contacts]
        },
        participantsSplit(participants, nb) {
            if (participants.length <= nb) {
                return [participants]
            }

            let array = [];

            let i = 0;
            let splitArray = [];
            participants.forEach(p => {
                splitArray.push(p)
                
                if (i % nb === nb - 1) {
                    array.push(splitArray);
                    splitArray = []
                }
                ++i;
            })

            array.push(splitArray);

            return array
            
        },
        getInitiales(talent, type) {
            if (type === 'contact') {
                return talent.email?.slice(0, 2).toUpperCase();
            }

            return talent.firstname.charAt(0) + talent.lastname.charAt(0);
        },
        appointmentViewed(appointmentId, userId, appointmentElementId, isNotViewed) {
            let appointmentElement = document.getElementById(appointmentElementId);
            if (isNotViewed) {
                axios.put(`api/viewed/appointment`, { params : {'appointmentId' : appointmentId, 'userId' : userId, 'appointmentElementId' : appointmentElementId}}).then(res => {
                    if (res) {
                        appointmentElement.classList.remove("app-is-waiting");
                        this.$bus.$emit("APPOINTMENT_UPDATE", appointmentId);
                    }
                });
            }
        }
    },
}
</script>

<style lang="scss">
.appointment-nav {
    .appointment-edit {
        margin-top: -5px;
    }

    .appointment-notes_show {
        margin-bottom: -4px;
    }
}

// @todo: find a better way than overwritting it
div.avatar-user-list {
    margin-left: 1rem; // same as the title
    margin-top:2rem;

    ul li .img-rdv-dash {
        margin-top: 0;
        margin-right: -4px;
    }

    li {
        width: 24px;
        height: 24px;
        margin-bottom: 10px;
    }
}

.tooltiped .tooltiptext {
    width: max-content;
    z-index: 100;

    &.title {
        max-width: 150px;
    }
}

.rdv__card {
    margin: 9px;
    margin-top:-4px;
    height:auto;
}
.rdv__contact {
    padding-top: 10px;
    padding-bottom: 10px;
    padding-left: 15px;
}

.rdv__contact a:hover {
    flex: 0;
}
.plan-wrapper2 {
    display: block;
    padding: 4px 0px;
    overflow: auto;
    background: #2b1c56;
    border-radius: 10px;
    z-index: 1;
    height: 550px;
}

.see-more-btn {
    cursor: pointer;

    .img-rdv-dash {
        background-color: #1E1438;
    }

    .user-initials {
        color:#7665A7;
        padding-bottom: 3px;
    }
}
</style>